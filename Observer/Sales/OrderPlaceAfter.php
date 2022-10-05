<?php                                                         

namespace Usercom\Analytics\Observer\Sales;                        

class OrderPlaceAfter implements \Magento\Framework\Event\ObserverInterface                                
{

    protected $helper;
    protected $usercom; 
    protected $addressConfig;


    public function __construct(
        \Usercom\Analytics\Helper\Data $helper,
        \Usercom\Analytics\Helper\Usercom $usercom,
        \Magento\Customer\Model\Address\Config $addressConfig
    ){
        $this->helper = $helper;
        $this->usercom = $usercom;
        $this->addressConfig = $addressConfig;
    }

    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {

        if( !$this->helper->isModuleEnabled() || !($usercomCustomerId = $this->usercom->getUsercomCustomerId()) )
            return;

        $postData = (array)json_decode(file_get_contents('php://input'));
        unset($postData["email"]);
        unset($postData["paymentMethod"]);
        unset($postData["billingAddress"]);

        $this->usercom->updateCustomer($usercomCustomerId,array("attributes"=>$postData));

        $order = $observer->getEvent()->getOrder();
        $orderData = $order->getData();
        unset($orderData["addresses"]); 
        unset($orderData["status_histories"]);
        unset($orderData["payment"]);
        unset($orderData["extension_attributes"]); 
        
        $address = $this->addressConfig->getFormatByCode(\Magento\Customer\Model\Address\Config::DEFAULT_ADDRESS_FORMAT)->getRenderer(); 
        $orderData["shipping_address"] = $address->renderArray($order->getShippingAddress());
        $orderData["billing_address"] = $address->renderArray($order->getBillingAddress());
        $orderData["payment"] = json_encode(print_r($order->getPayment()->getMethodInstance()->getTitle(),true));

        $orderData["items"] = "";
        foreach ($order->getAllItems() as $item)
            $orderData["items"] .= $item->getName().",";
        $orderData["items"] = trim($orderData["items"],","); 
            
        $data = array(
            "user_id" => $usercomCustomerId,
            "name" => "order",
            "timestamp" => time(),
            "data" => array_merge($postData,$orderData,array(
                "step" => "order_completed"
            ))
        );

        $this->usercom->createEvent($data);
    }
}
