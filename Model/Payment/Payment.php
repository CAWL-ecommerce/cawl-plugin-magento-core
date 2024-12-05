<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Model\Payment;

use Magento\Framework\Model\AbstractModel;
use Cawl\PaymentCore\Api\Data\PaymentInterface;
use Cawl\PaymentCore\Model\Payment\ResourceModel\Payment as PaymentResource;

class Payment extends AbstractModel implements PaymentInterface
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'worldline_payment';

    protected function _construct(): void
    {
        $this->_init(PaymentResource::class);
    }
}
