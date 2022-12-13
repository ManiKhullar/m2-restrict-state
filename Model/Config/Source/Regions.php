<?php

declare(strict_types=1);

namespace Bluethink\RestrictState\Model\Config\Source;

use Magento\Directory\Api\CountryInformationAcquirerInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Regions for showing Allowded country states
 */
class Regions implements OptionSourceInterface
{
    /**
     * @var CountryInformationAcquirerInterface
     */
    protected $countryInformationAcquirer;

    /**
     * Regions Constructor
     *
     * @param CountryInformationAcquirerInterface $countryInformationAcquirer
     */
    public function __construct(
        CountryInformationAcquirerInterface $countryInformationAcquirer
    ) {
        $this->countryInformationAcquirer = $countryInformationAcquirer;
    }

    /**
     * Return Option for Region
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $countries = $this->countryInformationAcquirer->getCountriesInfo();
        $regions[] = [
            'value' => '',
            'label' => 'Select State',
        ];
        foreach ($countries as $country) {
            if ($availableRegions = $country->getAvailableRegions()) {
                foreach ($availableRegions as $region) {
                    $regions[] = [
                        'value' => $region->getName(),
                        'label' => $region->getName() . ' (' . $country->getFullNameLocale() .')',
                    ];
                }
            }
        }
        return $regions;
    }
}
