<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Cawl\PaymentCore\Api\Config\GeneralSettingsConfigInterface;
use Cawl\PaymentCore\Model\Order\ValidatorPool\DiscrepancyValidator;

class SetPaymentReviewStatus implements ObserverInterface
{
    /**
     * @var DiscrepancyValidator
     */
    private $discrepancyValidator;

    /**
     * @var GeneralSettingsConfigInterface
     */
    private $generalSettings;

    public function __construct(
        DiscrepancyValidator $discrepancyValidator,
        GeneralSettingsConfigInterface $generalSettings
    ) {
        $this->discrepancyValidator = $discrepancyValidator;
        $this->generalSettings = $generalSettings;
    }

    public function execute(Observer $observer): void
    {
        if (!$this->generalSettings->isAmountDiscrepancyEnabled()) {
            return;
        }

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
