<?php

namespace Usercom\Analytics\Controller\Adminhtml\System\Config;


class SyncCustomer extends \Magento\Backend\App\Action{

    protected $resultJsonFactory;
    protected $syncTimeArray;
    protected $customerFactory;
    protected $usercom;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Usercom\Analytics\Block\System\Config\SyncTime $syncTime,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Usercom\Analytics\Helper\Usercom $usercom
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->syncTimeArray = $syncTime->toOptionArray();
        $this->customerFactory = $customerFactory;
        $this->usercom = $usercom;
        parent::__construct($context);
    }


    public function execute(){

        if( !isset($_POST["time"]) ||
            !($time = $_POST["time"]) ||
            !(gettype($key = array_search($time,array_column($this->syncTimeArray, 'value'))) == "integer") ||
            !(array_search($time,array_column($this->syncTimeArray, 'value')) >= 0) ||
            !(array_search($time,array_column($this->syncTimeArray, 'value')) <= count($this->syncTimeArray))
        ) 
        return $this->result("Error: bad time", 400);


        $to = date("Y-m-d");
        $from = strtotime($this->syncTimeArray[$key]["time"],strtotime($to));
        $from = date('Y-m-d h:i:s', $from);

        $customers = $this->customerFactory->create()
                                            ->getCollection()
                                            ->addAttributeToFilter('created_at', array('from' => $from))
                                            ->load();


        $errorMessage = "";

        foreach($customers as $customer){
            if(!($usercomCustomerId = $this->usercom->getUsercomCustomerId($customer->getId(), false)) ){
                $errorMessage .= "Can't create customer by id: ".$customer->getId();
            }
        }


        return ($errorMessage) ? $this->result($errorMessage,409) : $this->result("Success", 200); 

    }


    public function result($message,$code){

        $result = $this->resultJsonFactory->create();
        $result->setHttpResponseCode($code);
        return $result->setData(['status' => $message]);
    }


}
