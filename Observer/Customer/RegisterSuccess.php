<?php                                                         

namespace Usercom\Analytics\Observer\Customer;                        

class RegisterSuccess implements \Magento\Framework\Event\ObserverInterface                                
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
            CURLOPT_URL => "https://".$this->helper->getSubdomain().".user.com/api/public/users/update_or_create/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\n  \"custom_id\": \"".$customer->getId()."\",\n  \"first_name\": \"".$customer->getFirstname()."\",\n  \"last_name\": \"".$customer->getLastName()."\"\n}",
            CURLOPT_HTTPHEADER => array(
                "Accept: */*; version=2",
                "authorization: Token ". $this->helper->getToken(),
                "content-type: application/json"
           ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl); 

        curl_close($curl);
        //log
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($response);

    }
}
