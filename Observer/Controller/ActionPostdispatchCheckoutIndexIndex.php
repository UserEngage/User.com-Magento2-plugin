<?php                                                         

namespace Usercom\Analytics\Observer\Controller;                        

class ActionPostdispatchCheckoutIndexIndex implements \Magento\Framework\Event\ObserverInterface                                
{

    protected $helper;
    protected $usercom;
    protected $cart;
    protected $customerSession;

    public function __construct(
        \Usercom\Analytics\Helper\Data $helper,
        \Usercom\Analytics\Helper\Usercom $usercom,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Customer\Model\Session $customerSession   
    ){
        $this->helper = $helper;
        $this->usercom = $usercom;
        $this->cart = $cart;
        $this->customerSession = $customerSession;
    }

    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {

        if( !$this->helper->isModuleEnabled() || !($usercomCustomerId = $this->usercom->getUsercomCustomerId()) )
            return;

        $products = $this->cart->getQuote()->getAllItems();
        $userCustomId = ($this->customerSession->isLoggedIn()) ? base64_encode($this->customerSession->getCustomer()->getId()) : null;

        foreach ($products as $product) {

            $productId = $product->getProductId();

            if($product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE)
                continue;

            if(!($usercomProductId = $this->usercom->getUsercomProductId($productId)) )
                continue;

            $this->usercom->createProductEvent($usercomProductId,array(
                "id" => $usercomProductId,
                "user_custom_id" => $userCustomId,
                "user_id" => $usercomCustomerId,
                "data" => $this->usercom->getProductData($productId),
                "event_type" => "checkout",
                "timestamp" => time()
            ));    
        }


        $data = array(
            "user_id" => $usercomCustomerId,
            "name" => "order",
            "timestamp" => time(),
            "data" => array(
                "step" => 1
            )
        );

        $this->usercom->createEvent($data);
    }
}
