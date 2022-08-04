<?php                                                         

namespace Usercom\Analytics\Observer\Controller;                        

class ActionPostdispatchNewsletterManageSave implements \Magento\Framework\Event\ObserverInterface                                
{

    protected $helper;

    public function __construct(
        \Usercom\Analytics\Helper\Data $helper
    ){

        $this->helper = $helper;
    }

    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {

        if(!$this->helper->isModuleEnabled())
            return;

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info("newsletter");

    }
}
