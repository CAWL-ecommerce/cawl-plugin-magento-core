<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Infrastructure\Plugin\Service\Payment;

use OnlinePayments\Sdk\Domain\PaymentResponse;
use OnlinePayments\Sdk\Domain\PaymentResponseFactory;
use Cawl\PaymentCore\Api\Test\Infrastructure\ServiceStubSwitcherInterface;
use Cawl\PaymentCore\Service\Payment\GetPaymentService;
use Cawl\PaymentCore\Infrastructure\StubData\Service\Payment\GetPaymentServiceResponse;

class GetPaymentServiceMock
{
    /**
     * @var ServiceStubSwitcherInterface
     */
    private $serviceStubSwitcher;

    /**
     * @var PaymentResponseFactory
     */
    private $paymentResponseFactory;

    public function __construct(
        ServiceStubSwitcherInterface $serviceStubSwitcher,
        PaymentResponseFactory $paymentResponseFactory
    ) {
        $this->serviceStubSwitcher = $serviceStubSwitcher;
        $this->paymentResponseFactory = $paymentResponseFactory;
    }

    /**
     * @param GetPaymentService $subject
     * @param callable $proceed
     * @param string $paymentId
     * @param int|null $storeId
     * @return PaymentResponse
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundExecute(
        GetPaymentService $subject,
        callable $proceed,
        string $paymentId,
        ?int $storeId = null
    ): PaymentResponse {
        if ($this->serviceStubSwitcher->isEnabled()) {
            $response = $this->paymentResponseFactory->create();
            $response->fromJson(GetPaymentServiceResponse::getData($paymentId));

            return $response;
        }

        return $proceed($paymentId, $storeId);
    }
}
