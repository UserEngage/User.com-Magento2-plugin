<?php 
namespace Usercom\Analytics\Model;


class OrderStep2 {

    protected $helper;
    protected $usercom;

    public function __construct(
        \Usercom\Analytics\Helper\Usercom $usercom,
        \Usercom\Analytics\Helper\Data $helper
    ) {
        $this->usercom = $usercom;
        $this->helper = $helper;
    }


    /**
     * {@inheritdoc}
     */
    public function order($userKey){

        if( !$this->helper->isModuleEnabled() || !($usercomCustomerId = $this->usercom->getUsercomCustomerId()) )
            return;        

        $data = array(
            "user_id" => $usercomCustomerId,
            "name" => "order",
            "timestamp" => time(),
            "data" => array(
                "step" => 2
            )
        );

        $this->usercom->createEvent($data);
    }
}
