<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Model\ThreeDSecure;

use OnlinePayments\Sdk\Domain\ThreeDSecure;
use Cawl\PaymentCore\Api\Config\GeneralSettingsConfigInterface;

class ParamsHandler
{
    public const LOW_VALUE_EXEMPTION_TYPE = 'low-value';

    /**
     * @var GeneralSettingsConfigInterface
     */
    private $generalSettings;

    public function __construct(GeneralSettingsConfigInterface $generalSettings)
    {
        $this->generalSettings = $generalSettings;
    }

    public function handle(ThreeDSecure $threeDSecure, float $baseSubtotal, int $storeId): void
    {
        $isThreeDEnabled = $this->generalSettings->isThreeDEnabled($storeId);
        $isAuthExemptionEnabled = $this->generalSettings->isAuthExemptionEnabled($storeId);

        $threeDSecure->setSkipAuthentication(!$isThreeDEnabled);

        if (!$isThreeDEnabled) {
            return;
        }

        if ($isAuthExemptionEnabled) {
            $threeDSExemptedType = $this->generalSettings->getAuthExemptionType($storeId)
                ?? self::LOW_VALUE_EXEMPTION_TYPE;
            $threeDSExemptedAmount = $threeDSExemptedType === self::LOW_VALUE_EXEMPTION_TYPE
                ? $this->generalSettings->getAuthLowValueAmount($storeId)
                : $this->generalSettings->getAuthTransactionRiskAnalysisAmount($storeId);

            if ((float)$threeDSExemptedAmount >= $baseSubtotal) {
                $threeDSecure->setSkipAuthentication(true);
                $threeDSecure->setExemptionRequest($threeDSExemptedType);
                $threeDSecure->setSkipSoftDecline(false);
            }
        }

        if ($this->generalSettings->isEnforceAuthEnabled($storeId)) {
            $threeDSecure->setChallengeIndicator('challenge-required');
        }
    }
}
