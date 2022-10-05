<?php                                                         

namespace Usercom\Analytics\Observer\Checkout;                        

class CartAddProductComplete implements \Magento\Framework\Event\ObserverInterface                                
{

    protected $helper;
    protected $usercom;
    protected $request;
    protected $configurableProduct;
    protected $customerSession;

    public function __construct(
        \Usercom\Analytics\Helper\Data $helper,
        \Usercom\Analytics\Helper\Usercom $usercom,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableProduct,
        \Magento\Customer\Model\Session $customerSession
    ){
    
        $this->helper = $helper;
        $this->usercom = $usercom;
        $this->request = $request;
        $this->configurableProduct = $configurableProduct;
        $this->customerSession = $customerSession;
    }

    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        $product = $observer->getEvent()->getData('product');  
        $productData = $this->request->getParams();
        $productId = $product->getId();
        if ($product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE){
            $attributes = $productData['super_attribute'];
            $productId = $this->configurableProduct->getProductByAttributes($attributes, $product)->getId();
        }

        if( !$this->helper->isModuleEnabled() || !($usercomCustomerId = $this->usercom->getUsercomCustomerId()) || !($usercomProductId = $this->usercom->getUsercomProductId($productId)) )
            return;

        $this->usercom->createProductEvent($usercomProductId,array(
            "id" => $usercomProductId,
            "user_custom_id" => ($this->customerSession->isLoggedIn()) ? base64_encode($this->customerSession->getCustomer()->getId()) : null,
            "user_id" => $usercomCustomerId,
            "data" => array_merge($this->usercom->getProductData($productId), array("quantity" => (isset($productData['qty'])?$productData["qty"]:1))),
            "event_type" => "add to cart",
            "timestamp" => time()
        ));
    }
}
