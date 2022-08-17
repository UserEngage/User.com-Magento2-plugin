<?php                                                         

namespace Usercom\Analytics\Observer\Checkout;                        

class CartAddProductComplete implements \Magento\Framework\Event\ObserverInterface                                
{

    protected $helper;
    protected $usercom;
    protected $request;
    protected $configurableProduct;

    public function __construct(
        \Usercom\Analytics\Helper\Data $helper,
        \Usercom\Analytics\Helper\Usercom $usercom,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableProduct
    ){
    
        $this->helper = $helper;
        $this->usercom = $usercom;
        $this->request = $request;
        $this->configurableProduct = $configurableProduct;
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
            "user_custom_id" => $this->usercom->getCustomerData()["custom_id"],
            "user_id" => $usercomCustomerId,
            "data" => $this->usercom->getProductData($productId),
            "event_type" => "add to cart",
            "timestamp" => time()
        ));
    }
}
