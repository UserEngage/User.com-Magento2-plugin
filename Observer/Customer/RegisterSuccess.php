<?php

namespace Usercom\Analytics\Observer\Customer;

use Magento\Customer\Model\CustomerRegistry;

class RegisterSuccess implements \Magento\Framework\Event\ObserverInterface
{
    protected $helper;
    protected $usercom;
    protected $request;
    protected CustomerRegistry $customerRegistry;

    public function __construct(
        CustomerRegistry $customerRegistry,
        \Usercom\Analytics\Helper\Data $helper,
        \Usercom\Analytics\Helper\Usercom $usercom,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->customerRegistry = $customerRegistry;
        $this->helper           = $helper;
        $this->usercom          = $usercom;
        $this->request          = $request;
    }

    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        $customer = $observer->getEvent()->getData('customer');
        $customerModel = $this->customerRegistry->retrieve($customer->getId());
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


//        if( !$this->helper->isModuleEnabled() || !($usercomCustomerId = $this->usercom->getUsercomCustomerId($customerId)) )
//            return;

//        $postData = $this->request->getParams();
//        unset($postData["firstname"]);
//        unset($postData["lastname"]);
//        unset($postData["is_subscribed"]);
//        unset($postData["email"]);
//        unset($postData["password"]);
//        unset($postData["password_confirmation"]);

        $data = [
//            'user_hash' => $this->getUserHash($customerId),
//            "user_id" => $usercomCustomerId,
//            "name"      => "registration",
//            "timestamp" => time(),
//            "data"      => array_merge($this->usercom->getCustomerData($customerId), $postData)
        ];
//        $this->helper->set
//        $this->usercom->createEvent($data);
    }

}
