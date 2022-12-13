<?php

declare(strict_types=1);

namespace Bluethink\RestrictState\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Provider to fetch config value
 */
class Provider
{
    public const XML_PATH_STATE_FILTER = 'general/state_restriction/country_state_filter';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Provider Constructor
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

   /**
    * Get Restricted Regions from configuration
    *
    * @return null|array
    */
    public function getRestrictedRegions(): ?array
    {
        $restrictedStates = $this->getStoreValue(self::XML_PATH_STATE_FILTER);
        if ($restrictedStates) {
            return explode(",", $restrictedStates);
        }

        return [];
    }

    /**
     * Get store value from configuration
     *
     * @param string $configPath
     * @return int
     * @throws NoSuchEntityException
     */
    protected function getStoreValue(string $configPath): ?string
    {
        return $this->scopeConfig->getValue(
            $configPath,
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId()
        );
    }
}
