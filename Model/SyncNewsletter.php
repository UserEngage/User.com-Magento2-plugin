<?php 
namespace Usercom\Analytics\Model;
 
 
class SyncNewsletter {

	/**
	 * {@inheritdoc}
	 */
	public function sync($data)
	{
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/Usercom.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info(print_r($data,true));
        $logger->info(print_r($_POST,true));
        $logger->info(print_r(file_get_contents('php://input'),true));
    }
}
