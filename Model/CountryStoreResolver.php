<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStore\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Opengento\CountryStore\Api\CountryStoreResolverInterface;
use Opengento\CountryStore\Api\Data\CountryInterface;
use Opengento\CountryStore\Model\Mapper\CountryStoreMapperInterface;
use Opengento\CountryStore\Model\Store\GetStoreByCountryInterface;
use function in_array;

final class CountryStoreResolver implements CountryStoreResolverInterface
{
    private StoreManagerInterface $storeManager;

    private CountryStoreMapperInterface $countryStoreMapper;

    private GetStoreByCountryInterface $getStoreByCountry;

    public function __construct(
        StoreManagerInterface $storeManager,
        CountryStoreMapperInterface $countryStoreMapper,
        GetStoreByCountryInterface $getStoreByCountry
    ) {
        $this->storeManager = $storeManager;
        $this->countryStoreMapper = $countryStoreMapper;
        $this->getStoreByCountry = $getStoreByCountry;
    }

    public function getStoreAware(CountryInterface $country): StoreInterface
    {
        try {
            $store = $this->storeManager->getStore();
            if (!in_array($store->getCode(), $this->countryStoreMapper->getStoresByCountry($country), true)) {
                $store = $this->getStoreByCountry->getByWebsite($country, $this->storeManager->getWebsite());
            }
        } catch (LocalizedException $e) {
            throw new NoSuchEntityException(
                new Phrase('No such store for the country with code "%1"', [$country->getCode()]),
                $e
            );
        }

        return $store;
    }
}
