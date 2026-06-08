<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Api\Service\Services;

interface StoreConnectionServiceInterface
{
    public function execute(int $storeId): bool;
}
