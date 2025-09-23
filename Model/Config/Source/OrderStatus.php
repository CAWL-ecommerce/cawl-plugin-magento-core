<?php

namespace Cawl\PaymentCore\Model\Config\Source;

use Magento\Sales\Model\Config\Source\Order\Status as MagentoOrderStatus;

class OrderStatus extends MagentoOrderStatus
{
    public function toOptionArray()
    {
        $options = parent::toOptionArray();

        // Filter out the empty option (value == '')
        return array_filter($options, function ($option) {
            return $option['value'] !== '';
        });
    }
}
