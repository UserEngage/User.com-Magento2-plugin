<?php                                                         

namespace Usercom\Analytics\Observer\Sales;                        

class QuoteRemoveItem implements \Magento\Framework\Event\ObserverInterface                                
{

    protected $helper;
    protected $request;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Usercom\Analytics\Helper\Data $helper
    ){
        $this->request = $request;
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
            $logger->info("remove");
    }
}
