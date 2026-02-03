<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Cawl\PaymentCore\Model\Order\ValidatorPool\DiscrepancyValidator;

class SetPaymentReviewStatus implements ObserverInterface
{
    /**
     * @var DiscrepancyValidator
     */
    private $discrepancyValidator;

    public function __construct(DiscrepancyValidator $discrepancyValidator)
    {
        $this->discrepancyValidator = $discrepancyValidator;
    }

    public function execute(Observer $observer): void
    {
        $order = $observer->getEvent()->getOrder();

        if ($this->isOrderWithDiscrepancy($order)) {
            $order->getPayment()->setIsTransactionPending(true);
        }
    }

    private function isOrderWithDiscrepancy(Order $order): bool
    {
        return $this->discrepancyValidator->compareAmounts($order->getGrandTotal(), $order->getIncrementId());
    }
}
