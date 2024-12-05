<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Infrastructure\Plugin\Service\Services;

use OnlinePayments\Sdk\Domain\CalculateSurchargeRequest;
use OnlinePayments\Sdk\Domain\CalculateSurchargeResponse;
use OnlinePayments\Sdk\Domain\CalculateSurchargeResponseFactory;
use Cawl\PaymentCore\Api\Test\Infrastructure\ServiceStubSwitcherInterface;
use Cawl\PaymentCore\Infrastructure\StubData\Service\Services\SurchargeCalculationResponse;
use Cawl\PaymentCore\Service\Services\SurchargeCalculationService;

class SurchargeCalculationServiceMock
{
    /**
     * @var ServiceStubSwitcherInterface
     */
    private $serviceStubSwitcher;

    /**
     * @var CalculateSurchargeResponseFactory
     */
    private $calculateSurchargeResponseFactory;

    public function __construct(
        ServiceStubSwitcherInterface $serviceStubSwitcher,
        CalculateSurchargeResponseFactory $calculateSurchargeResponseFactory
    ) {
        $this->serviceStubSwitcher = $serviceStubSwitcher;
        $this->calculateSurchargeResponseFactory = $calculateSurchargeResponseFactory;
    }

    /**
     * @param SurchargeCalculationService $subject
     * @param callable $proceed
     * @param CalculateSurchargeRequest $requestBody
     * @param int|null $storeId
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundExecute(
        SurchargeCalculationService $subject,
        callable $proceed,
        CalculateSurchargeRequest $requestBody,
        ?int $storeId = null
    ): array {
        if ($this->serviceStubSwitcher->isEnabled()) {
            /** @var CalculateSurchargeResponse $calculateSurchargeResponse */
            $calculateSurchargeResponse = $this->calculateSurchargeResponseFactory->create();
            $calculateSurchargeResponse->fromJson(SurchargeCalculationResponse::getData($requestBody));
            return $calculateSurchargeResponse->getSurcharges();
        }

        return $proceed($requestBody, $storeId);
    }
}
