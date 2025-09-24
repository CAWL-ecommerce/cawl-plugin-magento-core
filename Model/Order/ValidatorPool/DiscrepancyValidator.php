<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Model\Order\ValidatorPool;

use Cawl\PaymentCore\Api\Data\PaymentInterface as WlPaymentInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Cawl\PaymentCore\Model\Order\CurrencyAmountNormalizer;

class DiscrepancyValidator
{
    /**
     * @var CurrencyAmountNormalizer
     */
    private $normalizer;

    public function __construct(CurrencyAmountNormalizer $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    /**
     * @param float $orderTotal
     * @param WlPaymentInterface $payment
     *
     * @return bool
     */
    public function compareAmounts(float $orderTotal, WlPaymentInterface $payment): bool
    {
        $paidAmount = $this->normalizer->normalize((float)$payment->getAmount(), $payment->getCurrency());

        return $orderTotal !== $paidAmount;
    }
}
