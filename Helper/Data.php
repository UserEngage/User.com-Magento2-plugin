<?php
namespace Usercom\Analytics\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    const XML_PATH              = 'usercom/';
    const XML_ENABLE            = 'usercom/general/enable';
    const XML_TOKEN             = 'usercom/general/token';
    const XML_API               = 'usercom/general/api';
    const XML_SUBDOMAIN         = 'usercom/general/subdomain';
    const XML_SENDSTORESOURCE   = 'usercom/general/sendStoreSource';

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
    }

    public function getConfigValue($field, $storeId = null){

        return $this->scopeConfig->getValue(
            $field, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId
        );
    }

    public function getGeneralConfig($code, $storeId = null){

        return $this->getConfigValue(
            self::XML_PATH . $code, $storeId
        );
    }

    public function isModuleEnabled(){

        return $this->getConfigValue(self::XML_ENABLE);
    }

    public function getToken(){

        return $this->getConfigValue(self::XML_TOKEN);
    }

    public function getApi(){

        return $this->getConfigValue(self::XML_API);
    }

    public function getSubdomain(){

        return $this->getConfigValue(self::XML_SUBDOMAIN);
    }

    public function sendStoreSource(){

        return $this->getConfigValue(self::XML_SENDSTORESOURCE);
    }
}
