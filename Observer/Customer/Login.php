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

        if(!$this->helper->isModuleEnabled())
            return;

        $customer = $observer->getEvent()->getData('customer');

        $data = array(
            "user_id" => $this->usercom->getCustomerByCustomId($customer->getId())->id,
            "name" => "login",
            "timestamp" => time(),
            "data" => array(
                "email" => $customer->getEmail()
            )
        );

        $response = $this->usercom->sendPostEvent("events/",$data);
    }
}
