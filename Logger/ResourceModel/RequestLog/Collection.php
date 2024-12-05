<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Logger\ResourceModel\RequestLog;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Cawl\PaymentCore\Logger\ResourceModel\RequestLog as RequestLogResource;
use Cawl\PaymentCore\Logger\RequestLog as RequestLogModel;

class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(RequestLogModel::class, RequestLogResource::class);
    }

    public function getIdFieldName(): string
    {
        return 'id';
    }
}
