<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Api;

use Cawl\PaymentCore\Api\Data\PaymentInfoInterface;

interface InfoFormatterInterface
{
    public function format(PaymentInfoInterface $paymentInfo): array;
}
