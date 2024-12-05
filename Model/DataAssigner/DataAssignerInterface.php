<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Model\DataAssigner;

use Magento\Quote\Api\Data\PaymentInterface;
use Cawl\PaymentCore\Api\Data\QuotePaymentInterface;

/**
 * Assigner interface for quote payment data
 */
interface DataAssignerInterface
{
    public function assign(
        PaymentInterface $payment,
        QuotePaymentInterface $wlQuotePayment,
        array $additionalInformation
    ): void;
}
