<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Api;

use Cawl\PaymentCore\Api\Data\SurchargingCreditmemoInterface;

interface SurchargingCreditmemoRepositoryInterface
{
    public function save(SurchargingCreditmemoInterface $surchargingCreditmemo): SurchargingCreditmemoInterface;

    public function getByCreditmemoId(int $creditmemoId): SurchargingCreditmemoInterface;

    public function getItemsByQuoteId(int $quoteId): array;
}
