<?php                                                         

namespace Usercom\Analytics\Observer\Newsletter;                        

class SubscriberSaveAfter implements \Magento\Framework\Event\ObserverInterface                                
{

    protected $helper;
    protected $usercom;
    protected $url;

    public function __construct(
        \Usercom\Analytics\Helper\Data $helper,
        \Usercom\Analytics\Helper\Usercom $usercom,
        \Magento\Framework\UrlInterface $url
    ){

        $this->helper = $helper;
        $this->usercom = $usercom;
        $this->url = $url;
    }

    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
            
        $subscriber = $observer->getEvent()->getSubscriber();
        $customerId = $subscriber->getCustomerId();

        if(!$this->helper->isModuleEnabled() || !($subscriber->isStatusChanged()) || !($usercomCustomerId = $this->usercom->getUsercomCustomerId($customerId)))
            return;

        $subscribeStatus = ($subscriber->getStatus() == 1);

        $this->usercom->updateCustomer($usercomCustomerId, array("unsubscribed" => !$subscribeStatus)); 

        $this->usercom->createEvent(array(
            "user_id" => $usercomCustomerId,
            "data" => array(
                "email" => $subscriber->getSubscriberEmail(),
                "place" => $this->url->getCurrentUrl()
            ),
            "name" => "newsletter_signup",
            "timestamp" => time()
        ));
    }
}
