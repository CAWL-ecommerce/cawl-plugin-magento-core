<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Api;

use OnlinePayments\Sdk\DataObject;
use Cawl\PaymentCore\Api\Data\FraudInterface;
use Cawl\PaymentCore\Api\Data\PaymentInterface;

/**
 * Manager interface for fraud entity
 */
interface FraudManagerInterface
{
    public function saveFraudInformation(DataObject $worldlineResponse, PaymentInterface $wlPayment): ?FraudInterface;
}
