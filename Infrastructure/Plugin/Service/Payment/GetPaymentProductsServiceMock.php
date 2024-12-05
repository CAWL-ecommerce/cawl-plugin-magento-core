<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Infrastructure\Plugin\Service\Payment;

use OnlinePayments\Sdk\Domain\GetPaymentProductsResponse;
use OnlinePayments\Sdk\Domain\GetPaymentProductsResponseFactory;
use OnlinePayments\Sdk\Merchant\Products\GetPaymentProductsParams;
use Cawl\PaymentCore\Api\Test\Infrastructure\ServiceStubSwitcherInterface;
use Cawl\PaymentCore\Infrastructure\StubData\Service\Payment\GetPaymentProductsServiceResponse;
use Cawl\PaymentCore\Service\Payment\GetPaymentProductsService;

class GetPaymentProductsServiceMock
{
    /**
     * @var ServiceStubSwitcherInterface
     */
    private $serviceStubSwitcher;

    /**
     * @var GetPaymentProductsResponseFactory
     */
    private $paymentProductsResponseFactory;

    public function __construct(
        ServiceStubSwitcherInterface $serviceStubSwitcher,
        GetPaymentProductsResponseFactory $paymentProductsResponseFactory
    ) {
        $this->serviceStubSwitcher = $serviceStubSwitcher;
        $this->paymentProductsResponseFactory = $paymentProductsResponseFactory;
    }

    /**
     * @param GetPaymentProductsService $subject
     * @param callable $proceed
     * @param GetPaymentProductsParams $queryParams
     * @param int|null $storeId
     * @return GetPaymentProductsResponse
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundExecute(
        GetPaymentProductsService $subject,
        callable $proceed,
        GetPaymentProductsParams $queryParams,
        ?int $storeId = null
    ): GetPaymentProductsResponse {
        if ($this->serviceStubSwitcher->isEnabled()) {
            $response = $this->paymentProductsResponseFactory->create();
            $response->fromJson(GetPaymentProductsServiceResponse::getData($queryParams));

            return $response;
        }

        return $proceed($queryParams, $storeId);
    }
}
