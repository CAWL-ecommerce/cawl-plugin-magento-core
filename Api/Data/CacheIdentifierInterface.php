<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Api\Data;

interface CacheIdentifierInterface
{
    public function getCacheIdentifier(): string;

    public function setCacheIdentifier(string $cacheIdentifier): void;
}
