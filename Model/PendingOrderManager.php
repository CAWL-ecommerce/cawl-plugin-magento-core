<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Model;

use Cawl\PaymentCore\Api\Config\GeneralSettingsConfigInterface;
use Cawl\PaymentCore\Api\PaymentRepositoryInterface;
use Exception;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Quote\Model\QuoteManagement;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\OrderFactory;
use Psr\Log\LoggerInterface;
use Cawl\PaymentCore\Api\PaymentDataManagerInterface;
use Cawl\PaymentCore\Api\PendingOrderManagerInterface;
use Cawl\PaymentCore\Api\QuoteResourceInterface;
use Cawl\PaymentCore\Api\SessionDataManagerInterface;
use Cawl\PaymentCore\Api\SurchargingQuoteManagerInterface;
use Cawl\PaymentCore\Model\Order\CanPlaceOrderContextManager;
use Cawl\PaymentCore\Model\PaymentOrderManager\PaymentService;
use Cawl\PaymentCore\Model\Transaction\TransactionStatusInterface;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Validate payment information and create an order
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
class PendingOrderManager implements PendingOrderManagerInterface
{
    /**
     * @var SessionDataManagerInterface
     */
    private $sessionDataManager;

    /**
     * @var OrderFactory
     */
    private $orderFactory;

    /**
     * @var QuoteResourceInterface
     */
    private $quoteResource;

    /**
     * @var QuoteManagement
     */
    private $quoteManagement;

    /**
     * @var CanPlaceOrderContextManager
     */
    private $canPlaceOrderContextManager;

    /**
     * @var RefusedStatusProcessor
     */
    private $refusedStatusProcessor;

    /**
     * @var PaymentService
     */
    private $paymentService;

    /**
     * @var PaymentDataManagerInterface
     */
    private $paymentDataManager;

    /**
     * @var SurchargingQuoteManagerInterface
     */
    private $surchargingQuoteManager;

    /**
     * @var EventManager
     */
    private $eventManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var PaymentRepositoryInterface
     */
    private $wlPaymentRepository;

    /**
     * @var GeneralSettingsConfigInterface
     */
    private $generalSettings;

    public function __construct(
        SessionDataManagerInterface $sessionDataManager,
        OrderFactory $orderFactory,
        QuoteResourceInterface $quoteResource,
        QuoteManagement $quoteManagement,
        CanPlaceOrderContextManager $canPlaceOrderContextManager,
        RefusedStatusProcessor $refusedStatusProcessor,
        PaymentService $paymentService,
        PaymentDataManagerInterface $paymentDataManager,
        SurchargingQuoteManagerInterface $surchargingQuoteManager,
        EventManager $eventManager,
        LoggerInterface $logger,
        PaymentRepositoryInterface $wlPaymentRepository,
        GeneralSettingsConfigInterface $generalSettings
    ) {
        $this->sessionDataManager = $sessionDataManager;
        $this->orderFactory = $orderFactory;
        $this->quoteResource = $quoteResource;
        $this->quoteManagement = $quoteManagement;
        $this->canPlaceOrderContextManager = $canPlaceOrderContextManager;
        $this->refusedStatusProcessor = $refusedStatusProcessor;
        $this->paymentService = $paymentService;
        $this->paymentDataManager = $paymentDataManager;
        $this->surchargingQuoteManager = $surchargingQuoteManager;
        $this->eventManager = $eventManager;
        $this->logger = $logger;
        $this->wlPaymentRepository = $wlPaymentRepository;
        $this->generalSettings = $generalSettings;
    }

    public function processPendingOrder(string $incrementId): bool
    {
        $order = $this->orderFactory->create()->loadByIncrementId($incrementId);
        if ($order->getId()) {
            return true;
        }

        $quote = $this->quoteResource->getQuoteByReservedOrderId($incrementId);
        if (!$quote) {
            return false;
        }

        $payment = $quote->getPayment();
        if (!$payment->getAdditionalInformation('payment_id')) {
            $paymentIds = (array)$payment->getAdditionalInformation('payment_ids');
            $payment->setAdditionalInformation('payment_id', end($paymentIds));
            $this->quoteResource->save($quote);
        }

        $paymentResponse = $this->paymentService->getPaymentResponse($quote->getPayment());
        if (!$paymentResponse) {
            return false;
        }

        if ($surchargeSO = $paymentResponse->getPaymentOutput()->getSurchargeSpecificOutput()) {
            $this->surchargingQuoteManager->formatAndSaveSurchargingQuote($quote, $surchargeSO);
        }

        $quote->setTotalsCollectedFlag(false);
        $quote->collectTotals();

        $statusCode = (int)$paymentResponse->getStatusOutput()->getStatusCode();
        if ($statusCode === TransactionStatusInterface::WAITING_AUTHENTICATION) {
            return true;
        }

        $context = $this->canPlaceOrderContextManager->createContext($quote, $statusCode);
        if ($this->canPlaceOrderContextManager->canPlaceOrder($context)) {
            $this->paymentDataManager->savePaymentData($paymentResponse);
            if ($this->sessionDataManager->hasOrderCreationFlag($incrementId)) {
                return true;
            }
            $this->sessionDataManager->setOrderCreationFlag($incrementId);

            try {
                $order = $this->quoteManagement->submit($quote);
                if ($order && $this->isOrderWithDiscrepancy($order)) {
                    $orderDiscrepancyStatus = $this->generalSettings->getOrderDiscrepancyStatus();

                    $order->setState($orderDiscrepancyStatus)->setStatus($orderDiscrepancyStatus);
                    $order->save();
                }
                if (!$order) {
                    $this->refusedStatusProcessor->process($quote, $statusCode);
                    return false;
                }
            } catch (Exception $e) {
                $this->logger->error($e->getMessage(), ['reserved_order_id' => $incrementId]);
                $this->refusedStatusProcessor->process($quote, $statusCode);
                return false;
            }

            $this->eventManager->dispatch('checkout_submit_all_after', ['order' => $order, 'quote' => $quote]);
            $this->sessionDataManager->setOrderData($order);
            $this->sessionDataManager->setOrderCreationFlag(null);

            return true;
        }

        $this->refusedStatusProcessor->process($quote, $statusCode);

        return false;
    }

    /**
     * @param OrderInterface $order
     *
     * @return bool
     */
    private function isOrderWithDiscrepancy(OrderInterface $order): bool
    {
        $wlPayment = $this->wlPaymentRepository->get($order->getIncrementId());

        return $this->compareAmounts($order, $wlPayment);
    }

    /**
     * @param \Magento\Sales\Model\Order\Interceptor $order
     * @param PaymentInterface $payment
     *
     * @return bool
     */
    private function compareAmounts(\Magento\Sales\Model\Order\Interceptor $order, $payment): bool
    {
        $paidAmount = (float)$payment->getAmount()/100;

        return $order->getGrandTotal() !== $paidAmount;
    }
}
