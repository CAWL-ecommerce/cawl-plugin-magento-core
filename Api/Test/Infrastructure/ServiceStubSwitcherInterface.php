<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Api\Test\Infrastructure;

interface ServiceStubSwitcherInterface
{
    public function setEnabled(bool $enabled): ServiceStubSwitcherInterface;

    public function isEnabled(): bool;
}
