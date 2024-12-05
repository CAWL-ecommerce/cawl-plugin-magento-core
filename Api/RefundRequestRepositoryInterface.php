<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Api;

use Cawl\PaymentCore\Api\Data\RefundRequestInterface;

interface RefundRequestRepositoryInterface
{
    public function getListByIncrementId(string $incrementId): array;
    public function getByIncrementIdAndAmount(string $incrementId, int $amount): RefundRequestInterface;
    public function save(RefundRequestInterface $refundRequest): RefundRequestInterface;
}
