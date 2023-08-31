<?php

namespace Usercom\Analytics\Helper;

class Usercom extends \Magento\Framework\App\Helper\AbstractHelper
{
    const COOKIE_USERKEY = "userKey";
//    const COOKIE_USER_ID = "userComUserId";
    const DEBUG_USERCOM = false;
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
        $this->helper                   = $helper;
        $this->cookieManager            = $cookieManager;
        $this->storeManager             = $storeManager;
        $this->productRepositoryFactory = $productRepositoryFactory;
        $this->subscriber               = $subscriber;
        $this->customerSession          = $customerSession;
        $this->customer                 = $customer;
        $this->product                  = $product;
        $this->resourceConnection       = $resourceConnection;
        parent::__construct($context);
    }

    public function getCustomerById($id)
    {
        return $this->sendGetEvent('users/' . $id . '/');
    }

    public function sendGetEvent($url)
    {
        $ms   = microtime(true);
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL            => 'https://' . $this->helper->getSubdomain() . '/api/public/' . $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => "GET",
            CURLOPT_HTTPHEADER     => [
                "Accept: */*; version=2",
                "authorization: Token " . $this->helper->getToken()
            ],
        ]);

        $response = curl_exec($curl);
        $err      = curl_error($curl);
        $me       = microtime(true);

        $this->logRequest('sendGetEvent', $url, [], $me - $ms, $response);
//        $this->logError('sendGetEvent', $url, $err, $response);
        curl_close($curl);

        return ($err) ? null : json_decode($response);
    }

    /**
     * @param $data
     *
     * @return void
     */
    private function logRequest($name, $url, $data, $mt, $response): void
    {
        if (self::DEBUG_USERCOM) {
            file_put_contents(
                '/var/www/var/log/usercom.log',
                $name . ': ' . $mt . "\n" . $url . "\nREQUEST:   " . json_encode($data) . "\nRESPONSE:   " . $response . "\n\n\n\n\n",
                FILE_APPEND
            );
        }
    }

    public function findCustomerByEmail($email)
    {
        return $this->sendGetEvent('users/search/?email=' . $email);
    }

    public function updateOrCreateCustomer($data)
    {
        return $this->sendPostEvent("users/update_or_create/", $data);
    }

    public function sendPostEvent($url, $data)
    {
        $ms   = microtime(true);
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL            => "https://" . $this->helper->getSubdomain() . "/api/public/" . $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_POSTFIELDS     => json_encode($data),
            CURLOPT_HTTPHEADER     => [
                "Accept: */*; version=2",
                "authorization: Token " . $this->helper->getToken(),
                "content-type: application/json"
            ],
        ]);
        $response = curl_exec($curl);
        $err      = curl_error($curl);
        $me       = microtime(true);
        $this->logRequest('sendPostEvent', $url, $data, $me - $ms, $response);
//        var_dump($response);die;
//        $this->logError('sendPostEvent', $url, $err, $response);
        curl_close($curl);

        return ($err) ? null : json_decode($response);
    }

    public function updateCustomer($id, $data)
    {
        return $this->sendPutEvent("users/$id/", $data);
    }

    public function sendPutEvent($url, $data)
    {
        $ms   = microtime(true);
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL            => "https://" . $this->helper->getSubdomain() . "/api/public/" . $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => "PUT",
            CURLOPT_POSTFIELDS     => json_encode($data),
            CURLOPT_HTTPHEADER     => [
                "Accept: */*; version=2",
                "authorization: Token " . $this->helper->getToken(),
                "content-type: application/json"
            ],
        ]);

        $response = curl_exec($curl);
        $err      = curl_error($curl);
        $me       = microtime(true);
        $this->logRequest('sendGetEvent', $url, $data, $me - $ms, $response);
//        $this->logError('sendGetEvent', $url, $err, $response);
        curl_close($curl);

        return ($err) ? null : json_decode($response);
    }

    public function createProductEvent($id, $data)
    {
        return $this->sendPostEvent("products/$id/product_event/", $data);
    }

    public function createEvent($data)
    {
        if ($this->helper->sendStoreSource()) {
            $data["data"]["store_source"] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/";
        }

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

    public function sendDeleteEvent($url)
    {
        $ms = microtime(true);

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL            => "https://" . $this->helper->getSubdomain() . "/api/public/" . $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => "DELETE",
            CURLOPT_HTTPHEADER     => [
                "Accept: */*; version=2",
                "authorization: Token " . $this->helper->getToken(),
            ],
        ]);

        $response = curl_exec($curl);
        $err      = curl_error($curl);
        $me       = microtime(true);
        $this->logRequest('sendDeleteEvent', $url, [], $me - $ms, $response);
//        $this->logError('sendDeleteEvent', $url, $err, $response);
        curl_close($curl);

        return ($err) ? null : json_decode($response);
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
        ) {
            return $usercomCustomer->id;
            //else create customer
        } elseif ($customerId) {
            $data = array_merge($this->getCustomerData($customerId), ["custom_id" => base64_encode($customerId)]);

            //if customer created return customer id
            return (($usercomCustomer = $this->createCustomer($data)) && isset($usercomCustomer->id)) ? $usercomCustomer->id : false;
        } else {
            return null;
        }
    }

    public function getCustomerByCustomId($custom_id)
    {
        return $this->sendGetEvent('users-by-id/' . base64_encode($custom_id) . '/');
    }

    public function findCustomerByUserKey($userKey)
    {
        return $this->sendGetEvent("users/search/?key=$userKey");
    }

    public function getFrontUserKey()
    {
        return $this->cookieManager->getCookie(self::COOKIE_USERKEY);
    }

    public function getCustomerData($customerId = null)
    {
        //if not customerId but login
        if ($customerId == null && $this->customerSession->isLoggedIn()) {
            $customerId = $this->customerSession->getCustomer()->getId();
        }

        if ( ! $customerId) {
            return;
        }

        $customer = $this->customer->load($customerId);

        return [
            "custom_id"    => base64_encode($customerId),
            "first_name"   => $customer->getFirstname(),
            "last_name"    => $customer->getLastname(),
            "email"        => $customer->getEmail(),
            "unsubscribed" => ! $this->subscriber->loadByCustomerId($customerId)->isSubscribed(),
            "user_key"     => $this->getFrontUserKey()
        ];
    }

    public function createCustomer($data)
    {
        return $this->sendPostEvent("users/", $data);
    }

    public function setUserHash()
    {
        return $this->cookieManager->setPublicCookie(self::COOKIE_USER_ID);
    }

    public function getUsercomProductId($productId = null)
    {
        if ( ! $productId) {
            return false;
        }

        if (($usercomProduct = $this->getProductByCustomId($productId)) && isset($usercomProduct->id)) {
            return $usercomProduct->id;
        } else {
            $productData = $this->getProductData($productId);

            return (($usercomProduct = $this->createProduct($productData)) && isset($usercomProduct->id)) ? $usercomProduct->id : false;
        }
    }

    public function getProductByCustomId($custom_id)
    {
        return $this->sendGetEvent("products-by-id/$custom_id/details/");
    }

    public function getProductData($productId = null)
    {
        if ( ! $productId) {
            return;
        }

        $product = $this->product->load($productId);

        $data = [
            "custom_id"   => $productId,
            "name"        => $product->getName(),
            "price"       => (float)$product->getFinalPrice(),
            "product_url" => $product->getProductUrl(),
            "image_url"   => $this->storeManager->getStore()->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ) . 'catalog/product' . $product->getData('image')
        ];

        if ($categories = $product->getCategoryIds()) {
            $connection = $this->resourceConnection;
            $ccev       = $connection->getTableName('catalog_category_entity_varchar');
            $cce        = $connection->getTableName('catalog_category_entity');
            $ea         = $connection->getTableName('eav_attribute');
            $eet        = $connection->getTableName('eav_entity_type');

            $entityIdColumnExists = $connection->getConnection()->tableColumnExists($ccev, 'entity_id');
            if ($entityIdColumnExists === true) {
                $colName = 'entity_id';
            } else {
                $colName = 'row_id';
            }

            $query  = "SELECT GROUP_CONCAT(ccev.value SEPARATOR ', ') as 'categories'
            FROM " . $ccev . " ccev
            JOIN " . $cce . " cce
            ON cce." . $colName . " = ccev." . $colName . "
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
             ) and cce." . $colName . " in (" . implode(",", $categories) . ")";
            $result = $connection->getConnection()->fetchAll($query);
            if (isset($result[0]['categories'])) {
                $data["category_name"] = $result[0]['categories'];
            }
        }

        $attributes = $product->getAttributes();
        foreach ($attributes as $a) {
            $value = $product->getData($a->getName());
            if ($value != null) {
                $value               = (gettype($value) == "object" || gettype($value) == "array") ? json_encode($value) : strval($value);
                $data[$a->getName()] = $value;
            }
        }
        unset($data["media_gallery"]);

        return $data;
    }

    public function createProduct($data)
    {
        return $this->sendPostEvent("products/", $data);
    }

    /**
     * @param $customerId
     *
     * @return string
     */
    public function getUserHash($customerId): string
    {
        return $customerId . '_' . hash('sha256', $customerId . '-' . date('Y-m-d H:i:s') . $this->salt());
    }

    private function salt()
    {
        return 'usercom_salt';
    }

    private function logError(string $name, $url, string $err, $response)
    {
        if (self::DEBUG_USERCOM) {
            file_put_contents(
                '/var/www/var/log/usercom.log',
                $name . 'Error: ' . "\n" . $url . "\n" . json_encode($err) . "\n" . json_encode($response) . "\n",
                FILE_APPEND
            );
        }
    }
}
