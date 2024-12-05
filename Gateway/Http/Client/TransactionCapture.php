<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Gateway\Http\Client;

use Magento\Framework\Exception\LocalizedException;
use OnlinePayments\Sdk\Domain\CaptureResponse;
use Psr\Log\LoggerInterface;
use Cawl\PaymentCore\Gateway\Request\CaptureDataBuilder;
use Cawl\PaymentCore\Api\Service\Payment\CapturePaymentServiceInterface;

class TransactionCapture extends AbstractTransaction
{
    /**
     * @var CapturePaymentServiceInterface
     */
    private $capturePayment;

    public function __construct(
        LoggerInterface $logger,
        CapturePaymentServiceInterface $capturePayment
    ) {
        parent::__construct($logger);
        $this->capturePayment = $capturePayment;
    }

    /**
     * Execute capture transaction
     *
     * @param array $data
     * @return CaptureResponse
     * @throws LocalizedException
     */
    protected function process(array $data): CaptureResponse
    {
        return $this->capturePayment->execute(
            $data[CaptureDataBuilder::PAYMENT_ID],
            $data[CaptureDataBuilder::CAPTURE_PAYMENT_REQUEST],
            $data[CaptureDataBuilder::STORE_ID]
        );
    }
}
