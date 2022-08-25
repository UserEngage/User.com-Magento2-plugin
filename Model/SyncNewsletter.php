<?php
namespace Usercom\Analytics\Model;

class SyncNewsletter
{
    protected $customerRepository;
    protected $subscriber;
    protected $helper;

    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Newsletter\Model\Subscriber $subscriber,
        \Usercom\Analytics\Helper\Data $helper
    ) {
        $this->customerRepository = $customerRepository;
        $this->helper = $helper;
        $this->subscriber = $subscriber;
    }


    /**
     * {@inheritdoc}
     */
    public function sync($user)
    {
        $customerId = $this->customerRepository->get($user['email'],1)->getId();

        if ($user["emails_enabled"]) {
            $this->subscriber->subscribeCustomerById($customerId)->save();        
        } else {
            $this->subscriber->unsubscribeCustomerById($customerId)->save();  
        }
    }
}
