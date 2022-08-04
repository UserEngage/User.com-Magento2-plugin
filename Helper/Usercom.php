<?php
namespace Usercom\Analytics\Helper;

class Usercom extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $helper;

    public function __construct(
        \Usercom\Analytics\Helper\Data $helper,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->helper = $helper;
        parent::__construct($context);
    }

    public function sendEvent($url,$data){

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://".$this->helper->getSubdomain().".user.com/api/public/".$url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                "Accept: */*; version=2",
                "authorization: Token ".$this->helper->getToken(),
                "content-type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        return ($err) ?: $response;
    }
}
