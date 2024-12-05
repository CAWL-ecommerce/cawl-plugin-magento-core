<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Model\Webhook;

use Magento\Sales\Api\CreditmemoRepositoryInterface;
use Magento\Sales\Model\Order\Creditmemo;
use OnlinePayments\Sdk\Domain\RefundResponse;
use OnlinePayments\Sdk\Domain\WebhooksEvent;
use Cawl\PaymentCore\Api\QuoteResourceInterface;
use Cawl\PaymentCore\Api\RefundRequestRepositoryInterface;
use Cawl\PaymentCore\Api\TransactionWLResponseManagerInterface;
use Cawl\PaymentCore\Api\Webhook\ProcessorInterface;
use Cawl\PaymentCore\Model\RefundRequest\RefundRefusedNotification;
use Cawl\PaymentCore\Model\RefundRequest\RefundRefusedProcessor;
use Cawl\PaymentCore\Model\Transaction\TransactionStatusInterface;

class RejectPaymentProcessor implements ProcessorInterface
{
    /**
     * @var RefundRequestRepositoryInterface
     */
    private $refundRequestRepository;

    /**
     * @var TransactionWLResponseManagerInterface
     */
    private $transactionWLResponseManager;

    /**
     * @var RefundRefusedProcessor
     */
    private $refundRefusedProcessor;

    /**
     * @var CreditmemoRepositoryInterface
     */
    private $creditmemoRepository;

    /**
     * @var RefundRefusedNotification
     */
    private $refundRefusedNotification;

    /**
     * @var QuoteResourceInterface
     */
    private $quoteResource;

    public function __construct(
        RefundRequestRepositoryInterface $refundRequestRepository,
        TransactionWLResponseManagerInterface $transactionWLResponseManager,
        RefundRefusedProcessor $refundRefusedProcessor,
        CreditmemoRepositoryInterface $creditmemoRepository,
        RefundRefusedNotification $refundRefusedNotification,
        QuoteResourceInterface $quoteResource
    ) {
        $this->refundRequestRepository = $refundRequestRepository;
        $this->transactionWLResponseManager = $transactionWLResponseManager;
        $this->refundRefusedProcessor = $refundRefusedProcessor;
        $this->creditmemoRepository = $creditmemoRepository;
        $this->refundRefusedNotification = $refundRefusedNotification;
        $this->quoteResource = $quoteResource;
    }

    /**
     * Handled refused refund webhook only
     *
     * @param WebhooksEvent $webhookEvent
     * @return void
     */
    public function process(WebhooksEvent $webhookEvent): void
    {
        /** @var RefundResponse $refundResponse */
        $refundResponse = $webhookEvent->getRefund();
        if (!$refundResponse) {
            return;
        }

        $statusCode = (int)$refundResponse->getStatusOutput()->getStatusCode();
        if ($statusCode === TransactionStatusInterface::REFUND_REJECTED_CODE) {
            $incrementId = $refundResponse->getRefundOutput()->getReferences()->getMerchantReference();
            $amount = (int)$refundResponse->getRefundOutput()->getAmountOfMoney()->getAmount();
            $refundRequest = $this->refundRequestRepository->getByIncrementIdAndAmount((string)$incrementId, $amount);
            $creditmemoId = $refundRequest->getCreditMemoId();
            if (!$creditmemoId) {
                return;
            }

            $creditmemoEntity = $this->creditmemoRepository->get($creditmemoId);
            if ($creditmemoEntity->getState() == Creditmemo::STATE_CANCELED) {
                return;
            }

            $this->transactionWLResponseManager->saveTransaction($refundResponse);

            $this->refundRefusedProcessor->process($refundRequest);

            $quote = $this->quoteResource->getQuoteByReservedOrderId($incrementId);
            if (!$quote) {
                return;
            }

            $this->refundRefusedNotification->notify($quote, $incrementId, $creditmemoId);
        }
    }
}
