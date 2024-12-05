<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Api;

use Cawl\PaymentCore\Api\Data\FraudInterface;

/**
 * Repository interface for fraud entity
 */
interface FraudRepositoryInterface
{
    public function save(FraudInterface $fraudEntity): FraudInterface;

    public function getByIncrementId(string $incrementId): FraudInterface;
}
