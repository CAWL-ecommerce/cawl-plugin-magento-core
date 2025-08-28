<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Model;

use OnlinePayments\Sdk\Authentication\V1HmacAuthenticator;
use OnlinePayments\Sdk\Client;
use OnlinePayments\Sdk\ClientFactory;
use OnlinePayments\Sdk\CommunicatorConfigurationFactory;
use Cawl\PaymentCore\Api\ClientProviderInterface;
use Cawl\PaymentCore\Model\Config\WorldlineConfig;
use Cawl\PaymentCore\OnlinePayments\Sdk\Communicator;
use Cawl\PaymentCore\OnlinePayments\Sdk\CommunicatorFactory;

class ClientProvider implements ClientProviderInterface
{
    /**
     * @var Client|null
     */
    private $client;

    /**
     * @var CommunicatorConfigurationFactory
     */
    private $communicatorConfigurationFactory;

    /**
     * @var WorldlineConfig
     */
    private $worldlineConfig;

    /**
     * @var CommunicatorFactory
     */
    private $communicatorFactory;

    /**
     * @var ClientFactory
     */
    private $clientFactory;

    public function __construct(
        WorldlineConfig $worldlineConfig,
        CommunicatorConfigurationFactory $communicatorConfigurationFactory,
        CommunicatorFactory $communicatorFactory,
        ClientFactory $clientFactory
    ) {
        $this->worldlineConfig = $worldlineConfig;
        $this->communicatorConfigurationFactory = $communicatorConfigurationFactory;
        $this->communicatorFactory = $communicatorFactory;
        $this->clientFactory = $clientFactory;
    }

    public function getClient(?int $storeId = null): Client
    {
        if (!isset($this->client[$storeId])) {
            $this->client[$storeId] = $this->clientFactory->create(
                ['communicator' => $this->getCommunicator($storeId)]
            );
        }

        return $this->client[$storeId];
    }

    private function getCommunicator(?int $storeId = null): Communicator
    {
        $communicatorConfiguration = $this->communicatorConfigurationFactory->create([
            'apiKeyId' => $this->worldlineConfig->getApiKey($storeId),
            'apiSecret' => $this->worldlineConfig->getApiSecret($storeId),
            'apiEndpoint' => $this->worldlineConfig->getApiEndpoint($storeId),
            'integrator' => 'Ingenico',
        ]);

        $authenticator = new V1HmacAuthenticator($communicatorConfiguration);

        return $this->communicatorFactory->create(
            [
                'communicatorConfiguration' => $communicatorConfiguration,
                'authenticator' => $authenticator
            ]
        );
    }
}
