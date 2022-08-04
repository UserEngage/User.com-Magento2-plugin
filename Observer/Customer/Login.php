<?php                                                         

namespace Usercom\Analytics\Observer\Customer;                        

class Login implements \Magento\Framework\Event\ObserverInterface                                
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

        $customer = $observer->getEvent()->getData('customer');
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://".$this->helper->getSubdomain().".user.com/api/public/events/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\r\n   \"name\":\"login\",\r\n \"timestamp\":1426967129,\r\n  \"user_id\":3,\r\n   \"data\":{\r\n      \"keyData1\":\"value for key data 1\"\r\n   }\r\n}",
            CURLOPT_HTTPHEADER => array(
                "Accept: */*; version=2",
                "authorization: Token ".$this->helper->getToken(),
                "content-type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($response);

    }
}
