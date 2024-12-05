<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Api;

use Magento\Quote\Api\Data\CartInterface;
use OnlinePayments\Sdk\Domain\SurchargeSpecificOutput;

interface SurchargingQuoteManagerInterface
{
    public function saveSurchargingQuote(CartInterface $quote, float $surcharging): void;

    public function formatAndSaveSurchargingQuote(CartInterface $quote, SurchargeSpecificOutput $surchargeOutput): void;
}
