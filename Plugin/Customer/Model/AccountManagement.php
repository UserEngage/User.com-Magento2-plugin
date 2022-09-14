<?php                                                         

namespace Usercom\Analytics\Plugin\Customer\Model;                        

class AccountManagement
{
    protected $helper;
    protected $usercom; 
    protected $session;


    public function __construct(
        \Usercom\Analytics\Helper\Data $helper,
        \Usercom\Analytics\Helper\Usercom $usercom,
        \Magento\Checkout\Model\Session $session
    ){
        $this->helper = $helper;
        $this->usercom = $usercom;
        $this->session = $session;
    }

    public function beforeIsEmailAvailable(\Magento\Customer\Model\AccountManagement $subject, $customerEmail){
        
        $this->session->setGuestCustomerEmail($customerEmail);
    }
}
