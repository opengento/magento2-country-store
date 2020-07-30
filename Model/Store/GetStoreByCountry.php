<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStore\Model\Store;

use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Model\StoreManagerInterface;
use Opengento\CountryStore\Api\Data\CountryInterface;
use Opengento\CountryStore\Model\Mapper\CountryStoreMapperInterface;
use Psr\Log\LoggerInterface;
use function count;
use function in_array;

final class GetStoreByCountry implements GetStoreByCountryInterface
{
    private StoreManagerInterface $storeManager;

    private CountryStoreMapperInterface $countryStoreMapper;

    private RelatedWebsites $relatedWebsites;

    private LoggerInterface $logger;

    public function __construct(
        StoreManagerInterface $storeManager,
        CountryStoreMapperInterface $countryStoreMapper,
        RelatedWebsites $relatedWebsites,
        LoggerInterface $logger
    ) {
        $this->storeManager = $storeManager;
        $this->countryStoreMapper = $countryStoreMapper;
        $this->relatedWebsites = $relatedWebsites;
        $this->logger = $logger;
    }

    public function getByWebsite(CountryInterface $country, WebsiteInterface $website): StoreInterface
    {
        $websiteIds = $this->relatedWebsites->getListIds($website);
        $websitesCount = count($websiteIds);
        $stores = $this->countryStoreMapper->getStoresByCountry($country, $website);
        $relatedWebsite = null;

        for ($i = 0; $i < $websitesCount && !$stores; $i++) {
            $relatedWebsite = $this->storeManager->getWebsite($websiteIds[$i]);
            $stores = $this->countryStoreMapper->getStoresByCountry($country, $relatedWebsite);
        }

        $website = $stores && $relatedWebsite ? $relatedWebsite : $website;
        $store = $this->resolveDefaultStore($website);
        if ($stores && !in_array($store->getCode(), $stores, true)) {
            $store = $this->storeManager->getStore($stores[0]);
        }

        return $store;
    }

    private function resolveDefaultStore(WebsiteInterface $website): StoreInterface
    {
        try {
            return $this->storeManager->getStore(
                $this->storeManager->getGroup(
                    $website->getDefaultGroupId()
                )->getDefaultStoreId()
            );
        } catch (LocalizedException $e) {
            $this->logger->error($e->getLogMessage(), $e->getTrace());
        }

        return $this->storeManager->getDefaultStoreView();
    }
}
