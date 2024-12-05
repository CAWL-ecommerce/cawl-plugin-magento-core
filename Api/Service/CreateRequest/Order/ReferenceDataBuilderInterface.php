<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Api\Service\CreateRequest\Order;

use Magento\Quote\Api\Data\CartInterface;
use OnlinePayments\Sdk\Domain\OrderReferences;

interface ReferenceDataBuilderInterface
{
    public function build(CartInterface $quote): OrderReferences;
}
