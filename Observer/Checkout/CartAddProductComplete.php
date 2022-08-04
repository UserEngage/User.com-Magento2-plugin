<?php                                                         

namespace Usercom\Analytics\Observer\Checkout;                        

class CartAddProductComplete implements \Magento\Framework\Event\ObserverInterface                                
{

    protected $helper;
    protected $usercom;

    public function __construct(
        \Usercom\Analytics\Helper\Data $helper,
        \Usercom\Analytics\Helper\Usercom $usercom
    ){
    
        $this->helper = $helper;
        $this->usercom = $usercom;
    }

    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {

        if(!$this->helper->isModuleEnabled())
            return;

        $data = array(
            "name"=>"add_to_cart", 
            "user_id"=>"1", 
            "timestamp"=> time(),
            "data" => array(
                "sku" => 1
            )
        );

        $ret = $this->usercom->sendEvent("events/",$data);


        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info("add to cart: ".$ret);

    }
}
