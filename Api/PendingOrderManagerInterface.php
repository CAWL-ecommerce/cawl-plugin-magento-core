<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Api;

interface PendingOrderManagerInterface
{
    /**
     * @param string $incrementId
     * @return bool
     */
    public function processPendingOrder(string $incrementId): bool;
}
