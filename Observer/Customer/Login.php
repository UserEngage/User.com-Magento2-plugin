<?php

namespace Usercom\Analytics\Observer\Customer;

use Magento\Customer\Model\CustomerRegistry;

class Login implements \Magento\Framework\Event\ObserverInterface
{

    protected $helper;
    protected $usercom;
    protected CustomerRegistry $customerRegistry;

    public function __construct(
        CustomerRegistry $customerRegistry,
        \Usercom\Analytics\Helper\Data $helper,
        \Usercom\Analytics\Helper\Usercom $usercom
    ) {
        $this->customerRegistry = $customerRegistry;
        $this->helper           = $helper;
        $this->usercom          = $usercom;
    }

    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        $customerModel = $observer->getEvent()->getData('customer');
        $userUserId    = $customerModel->getData('usercom_user_id');
        if (is_null($userUserId)) {
            $hash = $this->usercom->getUserHash(
                $customerModel->getId()
            );
            $customerModel->setData(
                'usercom_user_id',
                $hash
            );
            $customerModel->save();
        }

//        $customer = $observer->getEvent()->getData('customer');
//
//        if( !$this->helper->isModuleEnabled() || !($usercomCustomerId = $this->usercom->getUsercomCustomerId($customer->getId())) )
//            return;
//
//        $this->usercom->updateCustomer($usercomCustomerId,$this->usercom->getCustomerData());
//
//        $data = array(
//            "user_id" => $usercomCustomerId,
//            "name" => "login",
//            "timestamp" => time(),
//            "data" => array(
//                "email" => $customer->getEmail()
//            )
//        );
//
//        $this->usercom->createEvent($data);
    }

    /**
     * @param $customerId
     *
     * @return string
     */
    private function getUserHash($customerId): string
    {
        return $customerId . '_' . hash('sha256', $customerId . '-' . date('Y-m-d H:i:s') . $this->salt());
    }

    private function salt()
    {
        return 'usercom_salt';
    }
}
