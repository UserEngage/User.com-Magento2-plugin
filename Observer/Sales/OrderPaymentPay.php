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
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);


        if(!$this->helper->isModuleEnabled())
            return;

        $order = $observer->getPayment()->getOrder();
        $customer = $this->customerRepositoryInterface->getById($order->getCustomerId());

        // create customer if not exist
        if(($usercomCustomerId = $this->usercom->findCustomerByEmail($order->getCustomerEmail())) && isset($usercomCustomerId->id)){
            
            $usercomCustomerId = $usercomCustomerId->id;
        }
        else {
            $data = array(
                "first_name" => $customer->getFirstName(),
                "last_name" => $customer->getLastName(),
                "email" => $customer->getEmail(),
                "custom_id" => $customer->getId()
            );

            $usercomCustomerId = $this->usercom->createCustomer($data)->id;
        }

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $products = array();
        foreach($order->getAllVisibleItems() as $product){

            //get all product category names
            $categoryName = "";
            $categoryIds = $objectManager->get('Magento\Catalog\Model\Product')->load($product->getProductId())->getCategoryIds();
            foreach($categoryIds as $categoryId)
                $categoryName .= $objectManager->create('Magento\Catalog\Model\Category')->load($categoryId)->getName().", ";
            $categoryName = rtrim($categoryName, ", ");

            $productData = array(
                "name" => $product->getName(),
                "price" => $product->getPrice(),
                "category" => $categoryName, 
                "product_url" => $objectManager->create('Magento\Catalog\Model\Product')->load($product->getProductId())->getProductUrl(),
                "image_url" => $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $this->productRepositoryFactory->create()->getById($product->getProductId())->getData('image'));


            //create product if not exist
            if(($usercomProductId = $this->usercom->getProductByCustomId($product->getProductId())) && isset($usercomProductId->id)){
                $usercomProductId = $usercomProductId->id;
            }
            else
                $usercomProductId  = $this->usercom->createProduct(array_merge($productData,array("custom_id" => $product->getProductId())))->id;


            //product purchase
            $this->usercom->createProductEvent($usercomProductId,array(
                "id" => $usercomProductId,
                "user_custom_id" => $customer->getId(),
                "user_id" => $usercomCustomerId,
                "data" => $productData,
                "event_type" => "purchase",
                "timestamp" => time()
            ));

            $products[] = array_merge($productData, array(
                "id" => $product->getProductId(),
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

        //user purchase
        $response = $this->usercom->sendPostEvent("events/",$data);

        $logger->info($response);

    }
}
