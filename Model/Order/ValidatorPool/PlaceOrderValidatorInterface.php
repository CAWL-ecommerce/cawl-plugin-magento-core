<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Model\Order\ValidatorPool;

use Magento\Framework\Exception\LocalizedException;
use Cawl\PaymentCore\Model\Order\CanPlaceContext;

/**
 * Validate if request data is valid for placing order
 */
interface PlaceOrderValidatorInterface
{
    /**
     * Validate if request data is valid for placing order
     *
     * @param CanPlaceContext $context
     * @return void
     * @throws LocalizedException
     */
    public function validate(CanPlaceContext $context): void;
}
