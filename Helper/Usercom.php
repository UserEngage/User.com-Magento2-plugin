<?php

namespace Usercom\Analytics\Helper;

class Usercom extends \Magento\Framework\App\Helper\AbstractHelper
{

    const COOKIE_USERKEY = "userKey";

    protected $helper;
    protected $cookieManager;
    protected $storeManager;
    protected $productRepositoryFactory;
    protected $subscriber;
    protected $customerSession;
    protected $customer;
    protected $product;
    protected $resourceConnection;

    public function __construct(
        \Usercom\Analytics\Helper\Data $helper,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Api\ProductRepositoryInterfaceFactory $productRepositoryFactory,
        \Magento\Newsletter\Model\Subscriber $subscriber,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->helper = $helper;
        $this->cookieManager = $cookieManager;
        $this->storeManager = $storeManager;
        $this->productRepositoryFactory = $productRepositoryFactory;
        $this->subscriber = $subscriber;
        $this->customerSession = $customerSession;
        $this->customer = $customer;
        $this->product = $product;
        $this->resourceConnection = $resourceConnection;
        parent::__construct($context);
    }

    public function sendPostEvent($url, $data)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://" . $this->helper->getSubdomain() . "/api/public/" . $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                "Accept: */*; version=2",
                "authorization: Token " . $this->helper->getToken(),
                "content-type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        return ($err) ? null : json_decode($response);
    }


    public function sendPutEvent($url, $data)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://" . $this->helper->getSubdomain() . "/api/public/" . $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                "Accept: */*; version=2",
                "authorization: Token " . $this->helper->getToken(),
                "content-type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        return ($err) ? null : json_decode($response);
    }


    public function sendGetEvent($url)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://' . $this->helper->getSubdomain() . '/api/public/' . $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Accept: */*; version=2",
                "authorization: Token " . $this->helper->getToken()
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        return ($err) ? null : json_decode($response);
    }


    public function sendDeleteEvent($url)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://" . $this->helper->getSubdomain() . "/api/public/" . $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_HTTPHEADER => array(
                "Accept: */*; version=2",
                "authorization: Token " . $this->helper->getToken(),
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        return ($err) ? null : json_decode($response);
    }

    public function getCustomerById($id)
    {

        return $this->sendGetEvent('users/' . $id . '/');
    }


    public function getCustomerByCustomId($custom_id)
    {

        return $this->sendGetEvent('users-by-id/' . base64_encode($custom_id) . '/');
    }


    public function findCustomerByEmail($email)
    {

        return $this->sendGetEvent('users/search/?email=' . $email);
    }

    public function createCustomer($data)
    {

        return $this->sendPostEvent("users/", $data);
    }

    public function updateOrCreateCustomer($data)
    {

        return $this->sendPostEvent("users/update_or_create/", $data);
    }

    public function updateCustomer($id, $data)
    {

        return $this->sendPutEvent("users/$id/", $data);
    }
    public function getProductByCustomId($custom_id)
    {

        return $this->sendGetEvent("products-by-id/$custom_id/details/");
    }

    public function createProduct($data)
    {

        return $this->sendPostEvent("products/", $data);
    }

    public function createProductEvent($id, $data)
    {

        return $this->sendPostEvent("products/$id/product_event/", $data);
    }

    public function findCustomerByUserKey($userKey)
    {

        return $this->sendGetEvent("users/search/?key=$userKey");
    }

    public function getFrontUserKey()
    {

        return $this->cookieManager->getCookie(self::COOKIE_USERKEY);
    }

    public function createEvent($data)
    {

        if ($this->helper->sendStoreSource())
            $data["data"]["store_source"] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/";

        return $this->sendPostEvent("events/", $data);
    }

    public function createCompany($data)
    {

        return $this->sendPostEvent("companies/", $data);
    }

    public function deleteCompany($id)
    {

        return $this->sendDeleteEvent("companies/$id");
    }

    public function companyRemoveMember($id, $data)
    {

        $this->sendPostEvent("companies/$id/remove_member/", $data);
    }

    public function companyAddMember($id, $data)
    {

        $this->sendPostEvent("companies/$id/add_member/", $data);
    }
    public function getUsercomCustomerId($customerId = null, $searchWithUserKey = true)
    {

        //if not customerId but login
        if ($customerId == null && $this->customerSession->isLoggedIn()) {
            $customerId = $this->customerSession->getCustomer()->getId();
        }

        //if customer exist in user.com
        if (($customerId && ($usercomCustomer = $this->getCustomerByCustomId($customerId)) && isset($usercomCustomer->id)) ||
            ($usercomCustomer = $this->findCustomerByUserKey($this->getFrontUserKey())) && isset($usercomCustomer->id) && $searchWithUserKey
        )
            return $usercomCustomer->id;

        //else create customer
        else if ($customerId) {
            $data = array_merge($this->getCustomerData($customerId), array("custom_id" => base64_encode($customerId)));
            //if customer created return customer id
            return (($usercomCustomer = $this->createCustomer($data)) && isset($usercomCustomer->id)) ? $usercomCustomer->id : false;
        } else
            return null;
    }

    public function getUsercomProductId($productId = null)
    {

        if (!$productId)
            return false;

        if (($usercomProduct = $this->getProductByCustomId($productId)) && isset($usercomProduct->id))
            return $usercomProduct->id;
        else {
            $productData = $this->getProductData($productId);
            return (($usercomProduct = $this->createProduct($productData)) && isset($usercomProduct->id)) ? $usercomProduct->id : false;
        }
    }

    public function getCustomerData($customerId = null)
    {

        //if not customerId but login
        if ($customerId == null && $this->customerSession->isLoggedIn()) {
            $customerId = $this->customerSession->getCustomer()->getId();
        }

        if (!$customerId)
            return;

        $customer = $this->customer->load($customerId);

        return array(
            "custom_id" => base64_encode($customerId),
            "first_name" => $customer->getFirstname(),
            "last_name" => $customer->getLastname(),
            "email" => $customer->getEmail(),
            "unsubscribed" => !$this->subscriber->loadByCustomerId($customerId)->isSubscribed(),
            "user_key" => $this->getFrontUserKey()
        );
    }

    public function getProductData($productId = null)
    {

        if (!$productId)
            return;

        $product = $this->product->load($productId);

        $data = array(
            "custom_id" => $productId,
            "name" => $product->getName(),
            "price" => (float)$product->getFinalPrice(),
            "product_url" => $product->getProductUrl(),
            "image_url" => $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $product->getData('image')
        );

        if ($categories = $product->getCategoryIds()) {
            $connection = $this->resourceConnection;
            $ccev = $connection->getTableName('catalog_category_entity_varchar');
            $cce = $connection->getTableName('catalog_category_entity');
            $ea = $connection->getTableName('eav_attribute');
            $eet = $connection->getTableName('eav_entity_type');
            $query = "SELECT GROUP_CONCAT(ccev.value SEPARATOR ', ') as 'categories'
            FROM " . $ccev . " ccev
            JOIN " . $cce . " cce
            ON cce.entity_id = ccev.entity_id
            AND ccev.attribute_id =
            (
                SELECT attribute_id
                FROM " . $ea . " ea
                WHERE attribute_code = 'name'
                and entity_type_id =
                (
                    SELECT entity_type_id
                    FROM " . $eet . " eet
                    WHERE entity_type_code = 'catalog_category'
                       )
             ) and cce.entity_id in (" . implode(",", $categories) . ")";
            $result = $connection->getConnection()->fetchAll($query);
            $data["category_name"] = $result[0]['categories'];
        }



        $attributes = $product->getAttributes();
        foreach ($attributes as $a) {
            $value = $product->getData($a->getName());
            if ($value != null) {
                $value = (gettype($value) == "object" || gettype($value) == "array") ? json_encode($value) : strval($value);
                $data[$a->getName()] = $value;
            }
        }
        unset($data["media_gallery"]);

        return $data;
    }
}
