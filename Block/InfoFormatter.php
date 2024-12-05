<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Block;

use Cawl\PaymentCore\Api\Data\PaymentInfoInterface;
use Cawl\PaymentCore\Api\InfoFormatterInterface;

class InfoFormatter implements InfoFormatterInterface
{
    public function format(PaymentInfoInterface $paymentInfo): array
    {
        return [
            [
                'label' => __('Total'),
                'value' => $paymentInfo->getAuthorizedAmount() . ' ' . $paymentInfo->getCurrency()
            ],
        ];
    }
}
