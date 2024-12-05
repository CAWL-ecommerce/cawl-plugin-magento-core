<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Api;

use Magento\Sales\Api\Data\CreditmemoInterface;

interface CreditmemoOnlineServiceInterface
{
    public function refund(CreditmemoInterface $creditmemo): CreditmemoInterface;
}
