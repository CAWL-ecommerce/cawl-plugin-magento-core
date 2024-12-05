<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Model\ResourceModel\EmailSendingList;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Cawl\PaymentCore\Model\EmailSendingList as EmailSenderModel;
use Cawl\PaymentCore\Model\ResourceModel\EmailSendingList as EmailSenderResource;

class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(EmailSenderModel::class, EmailSenderResource::class);
    }

    public function getIdFieldName(): string
    {
        return 'entity_id';
    }
}
