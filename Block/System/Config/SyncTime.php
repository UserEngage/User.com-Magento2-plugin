<?php

namespace Usercom\Analytics\Block\System\Config;

class SyncTime implements \Magento\Framework\Data\OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => '1', 'label' => __('3 months'),  "time" => "-3 month"],
            ['value' => '2', 'label' => __('6 months'),  "time" => "-6 month"],
            ['value' => '3', 'label' => __('12 months'), "time" => "-12 month"]
        ];
    }
}
