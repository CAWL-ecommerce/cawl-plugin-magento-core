<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Model\Payment\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Payment extends AbstractDb
{
    public const TABLE_NAME = 'cawl_payment';

    protected function _construct(): void
    {
        $this->_init(self::TABLE_NAME, 'entity_id');
    }
}
