<?php                                                         

namespace Usercom\Analytics\Observer\Catalog;                        

class ControllerProductView implements \Magento\Framework\Event\ObserverInterface                                
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
    
        $productId = $observer->getEvent()->getProduct()->getId();

        if( !$this->helper->isModuleEnabled() || !($usercomCustomerId = $this->usercom->getUsercomCustomerId()) || !($usercomProductId = $this->usercom->getUsercomProductId($productId)) )
            return;

         $this->usercom->createProductEvent($usercomProductId,array(
                "id" => $usercomProductId,
                "user_id" => $usercomCustomerId,
                "data" => $this->usercom->getProductData($productId),
                "event_type" => "view",
                "timestamp" => time()
            ));

    }
}
