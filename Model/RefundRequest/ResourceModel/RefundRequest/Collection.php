<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Model\RefundRequest\ResourceModel\RefundRequest;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Cawl\PaymentCore\Model\RefundRequest\RefundRequest as RefundRequestModel;
use Cawl\PaymentCore\Model\RefundRequest\ResourceModel\RefundRequest as RefundRequestResource;

class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(RefundRequestModel::class, RefundRequestResource::class);
    }

    public function getIdFieldName(): string
    {
        return 'id';
    }
}
