<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Api;

use Magento\Quote\Api\Data\CartInterface;
use Cawl\PaymentCore\Api\Data\CanPlaceOrderContextInterface;

interface CanPlaceOrderContextManagerInterface
{
    public function createContext(CartInterface $quote, int $statusCode): CanPlaceOrderContextInterface;

    public function canPlaceOrder(CanPlaceOrderContextInterface $context): bool;
}
