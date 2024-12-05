<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Service\Payment;

use OnlinePayments\Sdk\Domain\CapturePaymentRequest;
use OnlinePayments\Sdk\Domain\CapturePaymentRequestFactory;
use Cawl\PaymentCore\Api\Service\CapturePaymentRequestBuilderInterface;

class CapturePaymentRequestBuilder implements CapturePaymentRequestBuilderInterface
{
    /**
     * @var CapturePaymentRequestFactory
     */
    private $capturePaymentRequestFactory;

    public function __construct(
        CapturePaymentRequestFactory $capturePaymentRequestFactory
    ) {
        $this->capturePaymentRequestFactory = $capturePaymentRequestFactory;
    }

    public function build(int $amount): CapturePaymentRequest
    {
        $capturePaymentRequest = $this->capturePaymentRequestFactory->create();
        $capturePaymentRequest->setAmount($amount);

        return $capturePaymentRequest;
    }
}
