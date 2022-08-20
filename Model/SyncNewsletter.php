<?php 
namespace Usercom\Analytics\Model;


class SyncNewsletter {

    protected $subscriberFactory;
    protected $helper;

    public function __construct(
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        \Usercom\Analytics\Helper\Data $helper
    ) {
        $this->subscriberFactory= $subscriberFactory;
    }


    /**
     * {@inheritdoc}
     */
    public function sync($user)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/Usercom.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info(print_r($user,true));
/*

        if(!$this->helper->isModuleEnabled()
            return;
        if(!$user["unsubscribed"])
            $this->subscriberFactory->create()->subscribe($user["email"]);
        else
            $this->subscribeFactory->loadByEmail($user["email"])->unsubscribe();
 */
    }
}
