<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Api;

use OnlinePayments\Sdk\DataObject;
use Cawl\PaymentCore\Api\Data\PaymentInterface;

/**
 * Manager interface for worldline payment entity
 */
interface PaymentManagerInterface
{
    public function savePayment(DataObject $worldlineResponse): PaymentInterface;

    public function updatePayment(DataObject $worldlineResponse): PaymentInterface;
}
