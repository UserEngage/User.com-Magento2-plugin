<?php

namespace Usercom\Analytics\Controller\Adminhtml\System\Config;


class SyncCustomer extends \Magento\Backend\App\Action{

    protected $resultJsonFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }
   
   
    public function execute(){

        $result = $this->resultJsonFactory->create();
 
        return $result->setData(['success' => true, 'time' => 50]);
    }
}
