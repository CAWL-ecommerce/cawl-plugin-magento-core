<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Api\Webhook;

use OnlinePayments\Sdk\Domain\WebhooksEvent;

interface CustomProcessorStrategyInterface
{
    /**
     * Identify custom process by webhook content
     *
     * @param WebhooksEvent $webhookEvent
     * @return ProcessorInterface|null
     */
    public function getProcessor(WebhooksEvent $webhookEvent): ?ProcessorInterface;
}
