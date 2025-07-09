<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Api;

use Cawl\PaymentCore\Model\OrderState\OrderState;

interface OrderStateManagerInterface
{
    public function create(
        string $reservedOrderId,
        string $paymentCode,
        string $state,
        ?int $paymentProductId = null
    ): OrderState;
}
