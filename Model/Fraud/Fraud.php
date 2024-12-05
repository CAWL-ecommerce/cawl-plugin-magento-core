<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Model\Fraud;

use Magento\Framework\Model\AbstractModel;
use Cawl\PaymentCore\Api\Data\FraudInterface;
use Cawl\PaymentCore\Model\Fraud\ResourceModel\Fraud as FraudResource;

/**
 * Data model for fraud entity
 */
class Fraud extends AbstractModel implements FraudInterface
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'worldline_fraud_information';

    protected function _construct(): void
    {
        $this->_init(FraudResource::class);
    }
}
