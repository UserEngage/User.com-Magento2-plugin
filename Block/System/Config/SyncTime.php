<?php

namespace Usercom\Analytics\Block\System\Config;

class SyncTime implements \Magento\Framework\Data\OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => '1', 'label' => __('3 months')],
            ['value' => '2', 'label' => __('6 months')],
            ['value' => '3', 'label' => __('12 months')]
        ];
    }
}
