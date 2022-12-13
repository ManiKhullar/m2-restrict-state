<?php

declare(strict_types=1);

namespace Bluethink\RestrictState\Plugin;

use Magento\Directory\Helper\Data as DirectoryData;
use Magento\Framework\App\Cache\Type\Config;
use Magento\Framework\Json\Helper\Data as JsonData;
use Magento\Store\Model\StoreManagerInterface;
use Bluethink\RestrictState\Model\Config\Provider;

/**
 * Class RegionFilter for showing filtered states from Allowded states
 */
class RegionFilter
{
    /**
     * Disallowded States
     *
     * @var string
     */
    protected $disallowedStates;

    /**
     * RegionFilter constructor
     *
     * @param Provider $configProvider
     * @param StoreManagerInterface $storeManager
     * @param Config $configCacheType
     * @param JsonData $jsonHelper
     */
    public function __construct(
        Provider $configProvider,
        StoreManagerInterface $storeManager,
        Config $configCacheType,
        JsonData $jsonHelper
    ) {
        $this->configProvider = $configProvider;
        $this->storeManager = $storeManager;
        $this->configCacheType = $configCacheType;
        $this->jsonHelper = $jsonHelper;
    }

    /**
     * Get filtered states
     *
     * @param DirectoryData $subject
     * @param string $result
     * @return array
     */
    public function afterGetRegionJson(DirectoryData $subject, string $result): ?string
    {
        if ($result) {
            $cacheKey = 'DIRECTORY_REGIONS_JSON_STORE' . $this->storeManager->getStore()->getId();
            $json = $this->configCacheType->load($cacheKey);
            if (empty($json)) {
                $regionsData = $this->processRegion($subject);
                $json = $this->jsonHelper->jsonEncode($regionsData);
                if ($json === false) {
                    $json = 'false';
                }
                $this->configCacheType->save($json, $cacheKey);
            }
            $result = $json;
        }

        return $result;
    }

    /**
     * For Processing Region Data
     *
     * @param DirectoryData $subject
     * @return array
     */
    public function processRegion(DirectoryData $subject): array
    {
        $regions = $subject->getRegionData();
        $this->disallowedStates = $this->configProvider->getRestrictedRegions();
        foreach ($regions as $regionCode => $regionData) {
            if (isset($regions[$regionCode])) {
                $regions[$regionCode] = array_filter($regions[$regionCode], function ($region) {
                    if (isset($region['name'])) {
                        return !in_array($region['name'], $this->disallowedStates);
                    }
                    return true;
                });
            }
        }

        return $regions;
    }
}
