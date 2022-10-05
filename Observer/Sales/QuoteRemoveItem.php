<?php                                                         

namespace Usercom\Analytics\Observer\Sales;                        

class QuoteRemoveItem implements \Magento\Framework\Event\ObserverInterface                                
{

    protected $helper;
    protected $usercom;
    protected $customerSession;

    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Usercom\Analytics\Helper\Usercom $usercom,
        \Usercom\Analytics\Helper\Data $helper
    ){
        $this->customerSession = $customerSession;
        $this->usercom = $usercom;
        $this->helper = $helper;
    }

    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        
        $productId = $observer->getQuoteItem()->getProduct()->getId();
        if ($option = $observer->getQuoteItem()->getOptionByCode('simple_product')) {
            $productId = $option->getProduct()->getId();
        }
        
        if( !$this->helper->isModuleEnabled() || !($usercomCustomerId = $this->usercom->getUsercomCustomerId()) || !($usercomProductId = $this->usercom->getUsercomProductId($productId)) )
            return;

        $this->usercom->createProductEvent($usercomProductId,array(
            "id" => $usercomProductId,
            "user_custom_id" => ($this->customerSession->isLoggedIn()) ? base64_encode($this->customerSession->getCustomer()->getId()) : null,
            "user_id" => $usercomCustomerId,
            "data" => $this->usercom->getProductData($productId),
            "event_type" => "remove",
            "timestamp" => time()
        ));
    }
}
