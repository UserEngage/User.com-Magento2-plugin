<?php

namespace Usercom\Analytics\Block;

class Frontend extends \Magento\Framework\View\Element\Template
{
    protected $helper;
    protected $customerSession;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Usercom\Analytics\Helper\Data $helper
    ) {
        $this->customerSession = $customerSession;
        $this->helper          = $helper;
        parent::__construct($context);
    }

    public function isModuleEnabled()
    {
        return $this->helper->isModuleEnabled();
    }

    public function getApi()
    {
        return $this->helper->getApi();
    }

    public function getSubdomain()
    {
        return $this->helper->getSubdomain();
    }

    public function getUsercomUserId()
    {
        return ($this->customerSession->isLoggedIn()) ? $this->customerSession->getCustomer()->getData('usercom_user_id') : "";
    }

    public function getUsercomKey()
    {
        return ($this->customerSession->isLoggedIn()) ? $this->customerSession->getCustomer()->getData('usercom_user_key') : "";
    }
}
