<?php
namespace Usercom\Analytics\Block;

class Frontend extends \Magento\Framework\View\Element\Template
{
    protected $helper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Usercom\Analytics\Helper\Data $_helper
    ){
        $this->helper = $_helper;
        parent::__construct($context);
    }

    public function isModuleEnabled(){
        return $this->helper->isModuleEnabled();
    }

    public function getApi(){
        return $this->helper->getApi();
    }

    public function getSubdomain(){
        return $this->helper->getSubdomain();
    }
}
