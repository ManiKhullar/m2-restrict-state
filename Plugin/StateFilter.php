<?php

declare(strict_types=1);

namespace Bluethink\RestrictState\Plugin;

use Magento\Directory\Model\ResourceModel\Region\Collection;
use Bluethink\RestrictState\Model\Config\Provider;

/**
 * Class StateFilter for showing filtered states from Allowded states
 */
class StateFilter
{
    /**
     * @var string
     */
    protected $disallowedStates;

    /**
     * StateFilter constructor
     *
     * @param Provider $configProvider
     */
    public function __construct(
        Provider $configProvider
    ) {
        $this->configProvider = $configProvider;
    }

    /**
     * Get filtered states
     *
     * @param Collection $subject
     * @param array $options
     * @return array
     */
    public function afterToOptionArray(Collection $subject, array $options): array
    {
        $this->disallowedStates = $this->configProvider->getRestrictedRegions();
        $options = array_filter($options, function ($option) {
            if (isset($option['label'])) {
                return !in_array($option['label'], $this->disallowedStates);
            }
            return true;
        });
        return $options;
    }
}
