<?php                                                         

namespace Usercom\Analytics\Observer\Sales;                        

class OrderPaymentPay implements \Magento\Framework\Event\ObserverInterface                                
{

    protected $helper;
    protected $usercom;
    protected $productRepositoryFactory;
    protected $customerRepositoryInterface;

    public function __construct(
        \Usercom\Analytics\Helper\Data $helper,
        \Usercom\Analytics\Helper\Usercom $usercom,
        \Magento\Catalog\Api\ProductRepositoryInterfaceFactory $productRepositoryFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
    ){
        $this->helper = $helper;
        $this->usercom = $usercom;
        $this->productRepositoryFactory = $productRepositoryFactory;
        $this->storeManager =  $storeManager;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
    }

    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {

        if(!$this->helper->isModuleEnabled())
            return;

        $order = $observer->getPayment()->getOrder();
        $customerId = $this->customerRepositoryInterface->getById($order->getCustomerId())->getId();

        // create customer if not exist
        if(!($usercomCustomerId = $this->usercom->getUsercomCustomerId($customerId)))
            return;

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $products = array();

        foreach($order->getAllVisibleItems() as $product){

            $productId = $product->getProductId();

            if(!($usercomProductId = $this->usercom->getUsercomProductId($productId)))
                continue;

            $productData = $this->usercom->getProductData($productId); 

            $this->usercom->createProductEvent($usercomProductId,array(
                "id" => $usercomProductId,
                "user_custom_id" => $customerId,
                "user_id" => $usercomCustomerId,
                "data" => $productData,
                "event_type" => "purchase",
                "timestamp" => time()
            ));

            $products[] = array_merge($productData, array(
                "brand" => $product->getAttributeText('manufacturer'),
                "quantity" => (int)$product->getQtyOrdered(),
            ));

        }

        $data = array(
            "user_id" => $usercomCustomerId, 
            "name" => "purchase",
            "timestamp" => time(),
            "data" => array(
                "order_number" => $order->getId(),
                "revenue" => (float)$order->getGrandTotal(),
                "tax" => (float)$order->getTaxAmount(),
                "shipping" => (float)$order->getShippingAmount(),
                "currency" => $order->getOrderCurrencyCode(),
                "payment_method" => $order->getPayment()->getMethodInstance()->getTitle(),
                "coupon" => $order->getCouponCode(),
                "registered_user" => !$order->getCustomerIsGuest() ? true : false,
                "products" => $products
            )
        );

        $this->usercom->createEvent($data);
    }
}
