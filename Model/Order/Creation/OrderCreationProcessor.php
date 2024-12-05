<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Model\Order\Creation;

use Cawl\PaymentCore\Api\PendingOrderManagerInterface;
use Cawl\PaymentCore\Model\OutStockRefunder;
use Cawl\PaymentCore\Model\ResourceModel\PendingOrderProvider;
use Cawl\PaymentCore\Model\Order\FailedOrderCreationNotification;

class OrderCreationProcessor
{
    /**
     * @var PendingOrderProvider
     */
    private $quoteProvider;

    /**
     * @var PendingOrderManagerInterface
     */
    private $pendingOrderManager;

    /**
     * @var FailedOrderCreationNotification
     */
    private $failedOrderCreationNotification;

    /**
     * @var OutStockRefunder
     */
    private $outStockRefunder;

    public function __construct(
        PendingOrderProvider $quoteProvider,
        PendingOrderManagerInterface $pendingOrderManager,
        FailedOrderCreationNotification $failedOrderCreationNotification,
        OutStockRefunder $outStockRefunder
    ) {
        $this->quoteProvider = $quoteProvider;
        $this->pendingOrderManager = $pendingOrderManager;
        $this->failedOrderCreationNotification = $failedOrderCreationNotification;
        $this->outStockRefunder = $outStockRefunder;
    }

    public function process(?string $incrementOrderId = null): void
    {
        if ($incrementOrderId) {
            $this->placeOrder($incrementOrderId);
            return;
        }

        foreach ($this->quoteProvider->getReservedOrderIds() as $reservedOrderId) {
            $this->placeOrder((string) $reservedOrderId);
        }
    }

    private function placeOrder(string $reservedOrderId): void
    {
        $result = $this->pendingOrderManager->processPendingOrder($reservedOrderId);
        if ($result === false) {
            $this->failedOrderCreationNotification->notify(
                $reservedOrderId,
                'Sorry, but something went wrong',
                FailedOrderCreationNotification::WAITING_CRON_SPACE
            );
            $this->outStockRefunder->refundTransaction($reservedOrderId);
        }
    }
}
