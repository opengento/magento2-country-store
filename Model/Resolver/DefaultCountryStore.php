<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStore\Model\Resolver;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Opengento\CountryStore\Api\CountryRepositoryInterface;
use Opengento\CountryStore\Api\CountryResolverInterface;
use Opengento\CountryStore\Api\Data\CountryInterface;
use Opengento\CountryStore\Model\Mapper\CountryStoreMapperInterface;

use function in_array;

final class DefaultCountryStore implements CountryResolverInterface
{
    public const RESOLVER_CODE = 'defaultCountryStore';

    private const CONFIG_PATH_DEFAULT_COUNTRY = 'general/country/default';

    public function __construct(
        private ScopeConfigInterface $scopeConfig,
        private StoreManagerInterface $storeManager,
        private CountryStoreMapperInterface $countryStoreMapper,
        private CountryRepositoryInterface $countryRepository
    ) {}

    /**
     * @throws NoSuchEntityException
     */
    public function getCountry(): CountryInterface
    {
        $storeCountries = $this->countryStoreMapper->getCountriesByStore($this->storeManager->getStore());
        $countryCode = $this->scopeConfig->getValue(self::CONFIG_PATH_DEFAULT_COUNTRY, ScopeInterface::SCOPE_STORE);

        return $this->countryRepository->get(
            $storeCountries && !in_array($countryCode, $storeCountries, true) ? $storeCountries[0] : $countryCode
        );
    }
}
