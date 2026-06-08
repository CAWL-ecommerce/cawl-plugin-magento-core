<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Service\Services;

use Cawl\PaymentCore\Api\Config\WorldlineConfigInterface;
use Cawl\PaymentCore\Api\Service\Services\StoreConnectionServiceInterface;

class StoreConnectionService implements StoreConnectionServiceInterface
{
    /**
     * @var WorldlineConfigInterface
     */
    private $worldlineConfig;

    public function __construct(WorldlineConfigInterface $worldlineConfig)
    {
        $this->worldlineConfig = $worldlineConfig;
    }

    public function execute(int $storeId): bool
    {
        return (bool) $this->worldlineConfig->getMerchantId($storeId)
            && (bool) $this->worldlineConfig->getApiKey($storeId)
            && (bool) $this->worldlineConfig->getApiSecret($storeId);
    }
}
