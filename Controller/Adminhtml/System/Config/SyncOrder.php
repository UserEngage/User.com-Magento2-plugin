<?php

namespace Usercom\Analytics\Controller\Adminhtml\System\Config;


class SyncOrder extends \Magento\Backend\App\Action{

    protected $resultJsonFactory;
    protected $syncTimeArray;
    protected $orderCollectionFactory;
    protected $usercom;  
    protected $addressConfig;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Usercom\Analytics\Block\System\Config\SyncTime $syncTime,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Usercom\Analytics\Helper\Usercom $usercom,
        \Magento\Customer\Model\Address\Config $addressConfig
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->syncTimeArray = $syncTime->toOptionArray();
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->usercom = $usercom;
        $this->addressConfig = $addressConfig;
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

        $orders = $this->orderCollectionFactory->create()
                                               ->addAttributeToFilter('created_at', array('from' => $from))
                                               ->load();

        $errorMessage = "";

        foreach($orders as $order){
            $customerId = $order->getCustomerId();
            if(!($usercomCustomerId = $this->usercom->getUsercomCustomerId($customerId, false))){
                $errorMessage .= "Can't create user from order by id: ". $order->getId()."<br>";
                continue;
            }

            $orderData = $order->getData();
            unset($orderData["addresses"]); 
            unset($orderData["status_histories"]);
            unset($orderData["payment"]);
            unset($orderData["extension_attributes"]); 

            $orderData["shipping_address"] = $this->addressConfig->getFormatByCode(\Magento\Customer\Model\Address\Config::DEFAULT_ADDRESS_FORMAT)->getRenderer()->renderArray($order->getShippingAddress());
            $orderData["billing_address"] = $this->addressConfig->getFormatByCode(\Magento\Customer\Model\Address\Config::DEFAULT_ADDRESS_FORMAT)->getRenderer()->renderArray($order->getBillingAddress());
            $orderData["payment"] = json_encode(print_r($order->getPayment()->getMethodInstance()->getTitle(),true));

            $orderData["items"] = "";
            foreach ($order->getAllItems() as $item)
                $orderData["items"] .= $item->getName().",";
            $orderData["items"] = trim($orderData["items"],","); 


            $data = array(
                "user_id" => $usercomCustomerId,
                "name" => "order",
                "timestamp" => strtotime($order->getData("created_at")),
                "data" =>array_merge($orderData, array(
                    "synchronization" => "magento2"
                ))
            );
            if(!isset($this->usercom->createEvent($data)->created) ){
                $errorMessage .= "Can't create order by id: ".$order->getId()."<br>";
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
