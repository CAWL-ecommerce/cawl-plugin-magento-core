<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Model\SurchargingCreditmemo;

use Magento\Framework\Model\AbstractModel;
use Cawl\PaymentCore\Api\Data\SurchargingCreditmemoInterface;
use Cawl\PaymentCore\Model\SurchargingCreditmemo\ResourceModel;

class SurchargingCreditmemo extends AbstractModel implements SurchargingCreditmemoInterface
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'worldline_surcharging_creditmemo';

    protected function _construct(): void
    {
        $this->_init(ResourceModel\SurchargingCreditmemo::class);
    }
}
