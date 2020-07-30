<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStore\Model\Resolver;

use Magento\Framework\App\Config\ScopeConfigInterface;
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

    private ScopeConfigInterface $scopeConfig;

    private StoreManagerInterface $storeManager;

    private CountryStoreMapperInterface $countryStoreMapper;

    private CountryRepositoryInterface $countryRepository;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        CountryStoreMapperInterface $countryStoreMapper,
        CountryRepositoryInterface $countryRepository
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->countryStoreMapper = $countryStoreMapper;
        $this->countryRepository = $countryRepository;
    }

    public function getCountry(): CountryInterface
    {
        $store = $this->storeManager->getStore();
        $storeCountries = $this->countryStoreMapper->getCountriesByStore($store);
        $countryCode = (string) $this->scopeConfig->getValue(
            self::CONFIG_PATH_DEFAULT_COUNTRY,
            ScopeInterface::SCOPE_STORE,
            $store->getCode()
        );

        return $this->countryRepository->get(
            $storeCountries && !in_array($countryCode, $storeCountries, true) ? $storeCountries[0] : $countryCode
        );
    }
}
