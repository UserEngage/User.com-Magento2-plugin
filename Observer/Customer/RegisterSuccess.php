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

        $customerId = $observer->getEvent()->getData('customer')->getId();
        
        if( !$this->helper->isModuleEnabled() || !($usercomCustomerId = $this->usercom->getUsercomCustomerId($customerId)) )
            return;


        $data = array(
            "user_id" => $usercomCustomerId,
            "name" => "registration",
            "timestamp" => time(),
            "data" => $this->usercom->getCustomerData($customerId)
        );

        $this->usercom->createEvent($data);

        }
}
