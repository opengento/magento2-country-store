<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStore\Model\Mapper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Model\StoreManagerInterface;
use Opengento\CountryStore\Api\Data\CountryInterface;
use Psr\Log\LoggerInterface;
use function array_fill_keys;
use function array_merge_recursive;

final class CountryStoreMapper implements CountryStoreMapperInterface
{
    private const CONFIG_PATH_COUNTRY_STORE_MAP = 'country/information/store';

    private ScopeConfigInterface $scopeConfig;

    private SerializerInterface $serializer;

    private StoreManagerInterface $storeManager;

    private LoggerInterface $logger;

    private ?array $countryStoreMapper = null;

    private ?array $countriesByStore = null;

    private ?array $storesByCountry = null;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        SerializerInterface $serializer,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

    public function getCountriesByStore(StoreInterface $store): array
    {
        if (!$this->countriesByStore) {
            $this->loadMapper();
        }

        return $this->countriesByStore[$store->getCode()] ?? [];
    }

    public function getStoresByCountry(CountryInterface $country, ?WebsiteInterface $website = null): array
    {
        if (!$this->storesByCountry) {
            $this->loadMapper();
        }

        return $this->storesByCountry[$website ? $website->getCode() : '__all__'][$country->getCode()] ?? [];
    }

    private function loadMapper(): void
    {
        $this->countriesByStore = [];
        $storesByCountry = [[]];

        foreach ($this->resolveCountryStoreMapper() as $countryStoreMapper) {
            if (isset($countryStoreMapper['countries'], $countryStoreMapper['store'])) {
                $countries = (array) $countryStoreMapper['countries'];

                try {
                    $store = $this->storeManager->getStore((int) $countryStoreMapper['store']);
                } catch (NoSuchEntityException $e) {
                    $this->logger->error($e->getLogMessage(), $e->getTrace());
                    continue;
                }
                $this->countriesByStore[$store->getCode()] = $countries;

                try {
                    $website = $this->storeManager->getWebsite($store->getWebsiteId());
                } catch (LocalizedException $e) {
                    $this->logger->error($e->getLogMessage(), $e->getTrace());
                    continue;
                }
                $storeCountries = array_fill_keys($countries, [$store->getCode()]);
                $storesByCountry[] = [$website->getCode() => $storeCountries];
                $storesByCountry[] = ['__all__' => $storeCountries];
            }
        }

        $this->storesByCountry = array_merge_recursive(...$storesByCountry);
    }

    private function resolveCountryStoreMapper(): array
    {
        return $this->countryStoreMapper ?? $this->countryStoreMapper = $this->serializer->unserialize(
            $this->scopeConfig->getValue(self::CONFIG_PATH_COUNTRY_STORE_MAP) ?? '{}'
        );
    }
}
