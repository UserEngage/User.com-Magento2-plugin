<?php                                                         

namespace Usercom\Analytics\Observer\Sales;                        

class OrderPlaceAfter implements \Magento\Framework\Event\ObserverInterface                                
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

        if( !$this->helper->isModuleEnabled() || !($usercomCustomerId = $this->usercom->getUsercomCustomerId()) )
            return;

        $postData = (array)json_decode(file_get_contents('php://input'));
        unset($postData["email"]);
        unset($postData["paymentMethod"]);
        unset($postData["billingAddress"]);

        $this->usercom->updateCustomer($this->usercom->getUsercomCustomerId(),array("attributes"=>$postData));

        $data = array(
            "user_id" => $usercomCustomerId,
            "name" => "order",
            "timestamp" => time(),
            "data" => array_merge($postData,array(
                "step" => 3
            ))
        );

        $this->usercom->createEvent($data);
    }
}
