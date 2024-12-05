<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Model\Order;

use Cawl\PaymentCore\Model\Order\ValidatorPool\PlaceOrderValidatorInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Validate if request is valid for placing order
 */
class CanPlaceValidator
{
    /**
     * @var PlaceOrderValidatorInterface[]
     */
    private $validatorPool;

    public function __construct(
        array $validatorPool = []
    ) {
        $this->validatorPool = $validatorPool;
    }

    /**
     * Validate if request is valid for placing order
     *
     * @param CanPlaceContext $context
     * @return void
     * @throws LocalizedException
     */
    public function validate(CanPlaceContext $context): void
    {
        foreach ($this->validatorPool as $validator) {
            $validator->validate($context);
        }
    }
}
