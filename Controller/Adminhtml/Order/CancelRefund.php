<?php

namespace Cawl\PaymentCore\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\DB\Transaction;
use Cawl\PaymentCore\Service\Refund\CreateRefundService;
use OnlinePayments\Sdk\Domain\AmountOfMoney;
use OnlinePayments\Sdk\Domain\RefundRequest;

class CancelRefund extends Action
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;
    /**
     * @var Transaction
     */
    protected $transaction;
    /**
     * @var CreateRefundService
     */
    protected $createRefundService;

    public function __construct(
        Action\Context $context,
        JsonFactory $resultJsonFactory,
        OrderRepositoryInterface $orderRepository,
        Transaction $transaction,
        CreateRefundService $createRefundService
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->orderRepository = $orderRepository;
        $this->transaction = $transaction;
        $this->createRefundService = $createRefundService;
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $orderId = (int)$this->getRequest()->getParam('order_id');
        $paidAmount = (float)$this->getRequest()->getParam('paid_amount');
        $currency = $this->getRequest()->getParam('currency');
        $transactionId = $this->getRequest()->getParam('transaction_id');

        try {
            $order = $this->orderRepository->get($orderId);

            if (!$order->canCancel()) {
                return $result->setData([
                    'success' => false,
                    'message' => __('Order cannot be canceled.')
                ]);
            }

            $refundRequest = new RefundRequest();
            $amountOfMoney = new AmountOfMoney();
            $amountOfMoney->setAmount($paidAmount * 100);
            $amountOfMoney->setCurrencyCode($currency);
            $refundRequest->setAmountOfMoney($amountOfMoney);

            $this->createRefundService->execute($transactionId, $refundRequest, $order->getStoreId());

            // Cancel the order
            $order->cancel();

            // Add order comment about the discrepancy rejection
            $order->addCommentToStatusHistory(
                __("Order cancelled and fully refunded due to amount discrepancy.")
            )->setIsCustomerNotified(false);

            // Add discrepancy rejected flag to the order
            $order->setData('discrepancy_rejected', 1);

            $this->transaction->addObject($order)->save();

            return $result->setData([
                'success' => true,
                'message' => __('Order cancelled and fully refunded.')
            ]);

        } catch (\Exception $e) {
            return $result->setData([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
