<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Model\SurchargingQuote\ResourceModel\SurchargingQuote;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Cawl\PaymentCore\Model\SurchargingQuote\SurchargingQuote;
use Cawl\PaymentCore\Model\SurchargingQuote\ResourceModel\SurchargingQuote as SurchargingQuoteResource;

class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(SurchargingQuote::class, SurchargingQuoteResource::class);
    }
}
