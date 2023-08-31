<?php

namespace Usercom\Analytics\Observer\Catalog;

class ControllerProductView implements \Magento\Framework\Event\ObserverInterface
{

    protected $helper;
    protected $usercom;
    protected $customerSession;

    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Usercom\Analytics\Helper\Data $helper,
        \Usercom\Analytics\Helper\Usercom $usercom
    ){
        $this->customerSession = $customerSession;
        $this->helper = $helper;
        $this->usercom = $usercom;
    }

    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {

        $productId = $observer->getEvent()->getRequest()->getParam('id');

        if( !$this->helper->isModuleEnabled() || !($usercomCustomerId = $this->usercom->getUsercomCustomerId()) || !($usercomProductId = $this->usercom->getUsercomProductId($productId)) )
            return;

        $this->usercom->createProductEvent($usercomProductId,array(
            "id" => $usercomProductId,
            "user_custom_id" => ($this->customerSession->isLoggedIn()) ? base64_encode($this->customerSession->getCustomer()->getId()) : null,
            "user_id" => $usercomCustomerId,
            "data" => $this->usercom->getProductData($productId),
            "event_type" => "view",
            "timestamp" => time()
        ));

    }
}
