<?php                                                         

namespace Usercom\Analytics\Observer\Customer;                        

class Login implements \Magento\Framework\Event\ObserverInterface                                
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

        $customer = $observer->getEvent()->getData('customer');
        
        if( !$this->helper->isModuleEnabled() || !($usercomCustomerId = $this->usercom->getUsercomCustomerId($customer->getId())) )
            return;

        $this->usercom->updateCustomer($usercomCustomerId,$this->usercom->getCustomerData());

        $data = array(
            "user_id" => $usercomCustomerId,
            "name" => "login",
            "timestamp" => time(),
            "data" => array(
                "email" => $customer->getEmail()
            )
        );

        $this->usercom->createEvent($data);
    }
}
