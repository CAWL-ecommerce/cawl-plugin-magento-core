<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Model\Webhook;

use Cawl\PaymentCore\Model\AmountDiscrepancy\AmountDiscrepancyNotification;
use Cawl\PaymentCore\Model\Order\ValidatorPool\DiscrepancyValidator;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Quote\Model\QuoteManagement;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\OrderFactory;
use OnlinePayments\Sdk\Domain\WebhooksEvent;
use Psr\Log\LoggerInterface;
use Cawl\PaymentCore\Api\PaymentDataManagerInterface;
use Cawl\PaymentCore\Api\SessionDataManagerInterface;
use Cawl\PaymentCore\Api\SurchargingQuoteManagerInterface;
use Cawl\PaymentCore\Api\Webhook\ProcessorInterface;
use Cawl\PaymentCore\Model\Order\FailedOrderCreationNotification;
use Cawl\PaymentCore\Api\Webhook\PlaceOrderManagerInterface;
use Cawl\PaymentCore\Api\Data\PaymentProductsDetailsInterface;

/**
 * Identify if a webhook can trigger the order placement process, place an order and save payment information
 */
class PlaceOrderProcessor implements ProcessorInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var QuoteManagement
     */
    private $quoteManagement;

    /**
     * @var OrderFactory
     */
    private $orderFactory;

    /**
     * @var PaymentDataManagerInterface
     */
    private $paymentDataManager;

    /**
     * @var FailedOrderCreationNotification
     */
    private $failedOrderCreationNotification;

    /**
     * @var PlaceOrderManagerInterface
     */
    private $placeOrderManager;

    /**
     * @var SurchargingQuoteManagerInterface
     */
    private $surchargingQuoteManager;

    /**
     * @var EventManager
     */
    private $eventManager;

    /**
     * @var SessionDataManagerInterface
     */
    private $sessionDataManager;

    /**
     * @var DiscrepancyValidator
     */
    private $discrepancyValidator;


    /**
     * @var AmountDiscrepancyNotification
     */
    private $amountDiscrepancyNotification;

    public function __construct(
        LoggerInterface $logger,
        QuoteManagement $quoteManagement,
        OrderFactory $orderFactory,
        PaymentDataManagerInterface $paymentDataManager,
        FailedOrderCreationNotification $failedOrderCreationNotification,
        PlaceOrderManagerInterface $placeOrderManager,
        SurchargingQuoteManagerInterface $surchargingQuoteManager,
        EventManager $eventManager,
        SessionDataManagerInterface $sessionDataManager,
        DiscrepancyValidator $discrepancyValidator,
        AmountDiscrepancyNotification $amountDiscrepancyNotification
    ) {
        $this->logger = $logger;
        $this->quoteManagement = $quoteManagement;
        $this->orderFactory = $orderFactory;
        $this->paymentDataManager = $paymentDataManager;
        $this->failedOrderCreationNotification = $failedOrderCreationNotification;
        $this->placeOrderManager = $placeOrderManager;
        $this->surchargingQuoteManager = $surchargingQuoteManager;
        $this->eventManager = $eventManager;
        $this->sessionDataManager = $sessionDataManager;
        $this->discrepancyValidator = $discrepancyValidator;
        $this->amountDiscrepancyNotification = $amountDiscrepancyNotification;
    }

    public function process(WebhooksEvent $webhookEvent): void
    {
        if (!$this->shouldHandleEvent($webhookEvent)) {
            return;
        }
        $quote = $this->placeOrderManager->getValidatedQuote($webhookEvent);
        if (!$quote) {
            return;
        }

        $incrementId = (string)$quote->getReservedOrderId();
        $order = $this->orderFactory->create()->loadByIncrementId($incrementId);
        $this->paymentDataManager->savePaymentData($webhookEvent->getPayment());

        if ($order->getId() || !$webhookEvent->getPayment()) {
            return;
        }

        if ($surchargeSO = $webhookEvent->getPayment()->getPaymentOutput()->getSurchargeSpecificOutput()) {
            $this->surchargingQuoteManager->formatAndSaveSurchargingQuote($quote, $surchargeSO);
        }

        $quote->setTotalsCollectedFlag(false);
        $quote->collectTotals();

        try {
            if ($this->sessionDataManager->hasOrderCreationFlag($incrementId)) {
                return;
            }
            $this->sessionDataManager->setOrderCreationFlag($incrementId);

            $order = $this->quoteManagement->submit($quote);
            $wlPayment = $this->discrepancyValidator->getWlPayment($order->getIncrementId());
            if ($wlPayment && $this->isOrderWithDiscrepancy($order)) {
                $this->amountDiscrepancyNotification->notify($order, $wlPayment->getAmount());
            }

            if (!$order) {
                return;
            }

            $this->eventManager->dispatch('checkout_submit_all_after', ['order' => $order, 'quote' => $quote]);
            $this->sessionDataManager->setOrderCreationFlag(null);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), ['reserved_order_id' => $incrementId]);
            $this->sessionDataManager->setOrderCreationFlag(null);
            $this->failedOrderCreationNotification->notify(
                $quote->getReservedOrderId(),
                $e->getMessage(),
                FailedOrderCreationNotification::WEBHOOK_SPACE
            );
        }
    }

    /**
     * Determine if the event should be handled.
     *
     * @param WebhooksEvent $event
     *
     * @return bool
     */
    private function shouldHandleEvent(WebhooksEvent $event): bool
    {
        $paymentProductId = $this->getPaymentProductId($event);

        if ($this->isVoucherProduct($paymentProductId)) {
            return $this->hasEqualAmounts($event);
        }

        return true;
    }

    /**
     * Get payment product ID from event.
     *
     * @param WebhooksEvent $event
     *
     * @return int|null
     */
    private function getPaymentProductId(WebhooksEvent $event): ?int
    {
        $payment = $event->getPayment();
        if (!$payment) {
            return null;
        }

        $paymentOutput = $payment->getPaymentOutput();
        if (!$paymentOutput) {
            return null;
        }

        $redirectOutput = $paymentOutput->getRedirectPaymentMethodSpecificOutput();
        if (!$redirectOutput) {
            return null;
        }

        return $redirectOutput->getPaymentProductId();
    }

    /**
     * Check if amounts are equal for voucher products.
     *
     * @param WebhooksEvent $event
     *
     * @return bool
     */
    private function hasEqualAmounts(WebhooksEvent $event): bool
    {
        $payment = $event->getPayment();
        if (!$payment) {
            return false;
        }

        $paymentOutput = $payment->getPaymentOutput();
        if (!$paymentOutput) {
            return false;
        }

        $amountOfMoney = $paymentOutput->getAmountOfMoney()
            ? $paymentOutput->getAmountOfMoney()->getAmount()
            : null;

        $acquiredAmount = $paymentOutput->getAcquiredAmount()
            ? $paymentOutput->getAcquiredAmount()->getAmount()
            : null;

        return $amountOfMoney && $acquiredAmount && ($amountOfMoney === $acquiredAmount);
    }

    /**
     * Check if the payment product is a voucher type.
     *
     * @param int|null $paymentProductId
     *
     * @return bool
     */
    private function isVoucherProduct(?int $paymentProductId): bool
    {
        return in_array($paymentProductId, [
            PaymentProductsDetailsInterface::MEALVOUCHERS_PRODUCT_ID,
            PaymentProductsDetailsInterface::CHEQUE_VACANCES_CONNECT_PRODUCT_ID
        ], true);
    }

    /**
     * @param OrderInterface $order
     *
     * @return bool
     */
    private function isOrderWithDiscrepancy(OrderInterface $order): bool
    {
        return $this->discrepancyValidator->compareAmounts((float)$order->getGrandTotal(), $order->getIncrementId());
    }
}
