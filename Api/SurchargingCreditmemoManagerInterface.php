<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Api;

interface SurchargingCreditmemoManagerInterface
{
    public function createSurcharging(int $creditmemoId, int $quoteId, float $surchargingAmount): void;
}
