<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Setup\Patch\Data;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Cawl\PaymentCore\Model\Config\GeneralSettingsConfig;

class SetAuthExemptionDefaults implements DataPatchInterface
{
    const LOW_VALUE_EXEMPTION_TYPE = 'low-value';
    const LOW_VALUE_EXEMPTION_AMOUNT = '30';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var WriterInterface
     */
    private $configWriter;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        WriterInterface $configWriter
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->configWriter = $configWriter;
    }

    public function apply(): self
    {
        // save the values for all available websites
        foreach ($this->storeManager->getWebsites() as $website) {
            $websiteId = (int)$website->getId();

            if ($this->scopeConfig->getValue(GeneralSettingsConfig::AUTH_EXEMPTION, ScopeInterface::SCOPE_WEBSITES, $websiteId)) {
                $this->configWriter->save(
                    GeneralSettingsConfig::AUTH_EXEMPTION_TYPE,
                    self::LOW_VALUE_EXEMPTION_TYPE,
                    ScopeInterface::SCOPE_WEBSITES,
                    $websiteId
                );
                $this->configWriter->save(
                    GeneralSettingsConfig::AUTH_LOW_VALUE_AMOUNT,
                    self::LOW_VALUE_EXEMPTION_AMOUNT,
                    ScopeInterface::SCOPE_WEBSITES,
                    $websiteId
                );
            }
        }

        // set the values for the default config
        if ($this->scopeConfig->getValue(GeneralSettingsConfig::AUTH_EXEMPTION, ScopeInterface::SCOPE_WEBSITE, 0)) {
            $this->configWriter->save(
                GeneralSettingsConfig::AUTH_EXEMPTION_TYPE,
                self::LOW_VALUE_EXEMPTION_TYPE,
                'default',
                0
            );
            $this->configWriter->save(
                GeneralSettingsConfig::AUTH_LOW_VALUE_AMOUNT,
                self::LOW_VALUE_EXEMPTION_AMOUNT,
                'default',
                0
            );
        }

        return $this;
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }
}
