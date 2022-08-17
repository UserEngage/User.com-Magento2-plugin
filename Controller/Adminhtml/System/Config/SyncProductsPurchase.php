<?php

namespace Usercom\Analytics\Controller\Adminhtml\System\Config;


class SyncProductsPurchase extends \Magento\Backend\App\Action{

    protected $resultJsonFactory;
    protected $syncTimeArray;
    protected $orderCollectionFactory;
    protected $usercom;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Usercom\Analytics\Block\System\Config\SyncTime $syncTime,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Usercom\Analytics\Helper\Usercom $usercom
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->syncTimeArray = $syncTime->toOptionArray();
        $this->orderCollectionFactory = $orderCollectionFactory;
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

        $orders = $this->orderCollectionFactory->create()
                                               ->addAttributeToFilter('created_at', array('from' => $from))
                                               ->load();

        $errorMessage = "";

        foreach($orders as $order){
            $customerId = $order->getCustomerId();
            if(!($usercomCustomerId = $this->usercom->getUsercomCustomerId($customerId))){
                $errorMessage .= "Can't create user from order by id: ". $order->getId()."<br>";
                continue;
            }

            foreach($order->getAllItems() as $product){

                if($product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE)
                    continue;

                $productId = $product->getProductId();

                if(!($usercomProductId = $this->usercom->getUsercomProductId($productId))){
                    $errorMessage .= "Can't create product by id: ".$productId." from order by id: ". $order->getId()."<br>";
                    continue;
                }

                $productData = $this->usercom->getProductData($productId); 

                $data = array(
                    "id" => $usercomProductId,
                    "user_custom_id" => $customerId,
                    "user_id" => $usercomCustomerId,
                    "data" => $productData,
                    "event_type" => "purchase",
                    "timestamp" => time()
                );

                if(!isset($this->usercom->createProductEvent($usercomProductId,$data)->id) ){
                    $errorMessage .= "Can't create product by id: ".$productId." from order by id: ". $order->getId()."<br>";
                }
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
