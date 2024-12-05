<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Model\Payment\ResourceModel\Payment;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Cawl\PaymentCore\Model\Payment\Payment as PaymentModel;
use Cawl\PaymentCore\Model\Payment\ResourceModel\Payment as PaymentResource;

class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(PaymentModel::class, PaymentResource::class);
    }
}
