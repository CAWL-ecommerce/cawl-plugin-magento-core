<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Api\Service\Services;

interface TestConnectionServiceInterface
{
    public function execute(): string;
}
