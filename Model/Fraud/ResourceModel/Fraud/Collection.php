<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Model\Fraud\ResourceModel\Fraud;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Cawl\PaymentCore\Model\Fraud\Fraud as FraudModel;
use Cawl\PaymentCore\Model\Fraud\ResourceModel\Fraud as FraudResource;

/**
 * Collection for fraud entity
 */
class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(FraudModel::class, FraudResource::class);
    }
}
