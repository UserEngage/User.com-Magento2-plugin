<?php                                                         

namespace Usercom\Analytics\Observer\Customer;                        

class RegisterSuccess implements \Magento\Framework\Event\ObserverInterface                                
{

    protected $helper;
    protected $usercom;
    protected $request;

    public function __construct(
        \Usercom\Analytics\Helper\Data $helper,
        \Usercom\Analytics\Helper\Usercom $usercom,
        \Magento\Framework\App\RequestInterface $request
    ){
        
        $this->helper = $helper;
        $this->usercom = $usercom;
        $this->request = $request;
    }

    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {

        $customerId = $observer->getEvent()->getData('customer')->getId();
        
        if( !$this->helper->isModuleEnabled() || !($usercomCustomerId = $this->usercom->getUsercomCustomerId($customerId)) )
            return;
    
        $postData = $this->request->getParams();
        unset($postData["firstname"]);
        unset($postData["lastname"]);
        unset($postData["is_subscribed"]);
        unset($postData["email"]);
        unset($postData["password"]);
        unset($postData["password_confirmation"]);

        $data = array(
            "user_id" => $usercomCustomerId,
            "name" => "registration",
            "timestamp" => time(),
            "data" => array_merge($this->usercom->getCustomerData($customerId), $postData)
        );

        $this->usercom->createEvent($data);

        }
}
