<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Model\Webhook;

use OnlinePayments\Sdk\Domain\RefundResponse;
use OnlinePayments\Sdk\Domain\WebhooksEvent;
use Cawl\PaymentCore\Api\RefundRequestRepositoryInterface;
use Cawl\PaymentCore\Api\TransactionWLResponseManagerInterface;
use Cawl\PaymentCore\Api\Webhook\ProcessorInterface;
use Cawl\PaymentCore\Model\RefundRequest\RefundProcessor;
use Cawl\PaymentCore\Model\Transaction\TransactionStatusInterface;

class CreditmemoProcessor implements ProcessorInterface
{
    /**
     * @var RefundProcessor
     */
    private $refundProcessor;

    /**
     * @var RefundRequestRepositoryInterface
     */
    private $refundRequestRepository;

    /**
     * @var TransactionWLResponseManagerInterface
     */
    private $transactionWLResponseManager;

    public function __construct(
        RefundProcessor $refundProcessor,
        RefundRequestRepositoryInterface $refundRequestRepository,
        TransactionWLResponseManagerInterface $transactionWLResponseManager
    ) {
        $this->refundProcessor = $refundProcessor;
        $this->refundRequestRepository = $refundRequestRepository;
        $this->transactionWLResponseManager = $transactionWLResponseManager;
    }

    public function process(WebhooksEvent $webhookEvent): void
    {
        /** @var RefundResponse $refundResponse */
        $refundResponse = $webhookEvent->getRefund();
        $statusCode = (int)$refundResponse->getStatusOutput()->getStatusCode();
        if ($statusCode === TransactionStatusInterface::REFUND_UNCERTAIN_CODE) {
            return;
        }

        if ($statusCode === TransactionStatusInterface::REFUNDED_CODE) {
            $incrementId = $refundResponse->getRefundOutput()->getReferences()->getMerchantReference();
            $amount = (int)$refundResponse->getRefundOutput()->getAmountOfMoney()->getAmount();
            $refundRequest = $this->refundRequestRepository->getByIncrementIdAndAmount((string)$incrementId, $amount);
            if (!$refundRequest->getCreditMemoId()) {
                return;
            }

            $this->transactionWLResponseManager->saveTransaction($refundResponse);

            $this->refundProcessor->process($refundRequest);
        }
    }
}
