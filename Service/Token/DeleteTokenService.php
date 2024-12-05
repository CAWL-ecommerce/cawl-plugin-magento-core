<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Service\Token;

use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;
use Cawl\PaymentCore\Api\Service\Token\DeleteTokenServiceInterface;
use Cawl\PaymentCore\Api\ClientProviderInterface;
use Cawl\PaymentCore\Model\Config\WorldlineConfig;

class DeleteTokenService implements DeleteTokenServiceInterface
{
    /**
     * @var ClientProviderInterface
     */
    private $clientProvider;

    /**
     * @var WorldlineConfig
     */
    private $worldlineConfig;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        ClientProviderInterface $clientProvider,
        WorldlineConfig $worldlineConfig,
        LoggerInterface $logger
    ) {
        $this->clientProvider = $clientProvider;
        $this->worldlineConfig = $worldlineConfig;
        $this->logger = $logger;
    }

    public function execute(string $token, ?int $storeId = null): void
    {
        try {
            $this->clientProvider->getClient($storeId)
                ->merchant($this->worldlineConfig->getMerchantId($storeId))
                ->tokens()
                ->deleteToken($token);
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
            throw new LocalizedException(__('Cawl delete token has failed. Please contact the provider.'));
        }
    }
}
