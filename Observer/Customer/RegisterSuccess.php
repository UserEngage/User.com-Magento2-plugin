<?php                                                         

namespace Usercom\Analytics\Observer\Customer;                        

class RegisterSuccess implements \Magento\Framework\Event\ObserverInterface                                
{

    protected $helper;
    protected $usercom;

    public function __construct(
        \Usercom\Analytics\Helper\Data $helper,
        \Usercom\Analytics\Helper\Usercom $usercom
    ){
        
        $this->helper = $helper;
        $this->usercom = $usercom;
    }

    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {

        if(!$this->helper->isModuleEnabled())
            return;

        $customer = $observer->getEvent()->getData('customer');
        
        $data = array(
            "first_name" => $customer->getFirstName(),
            "last_name" => $customer->getLastName(),
            "email" => $customer->getEmail(),
            "custom_id" => $customer->getId()
        );

        $response = $this->usercom->sendEvent("/users/update_or_create/", $data);

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($response);

    }
}
