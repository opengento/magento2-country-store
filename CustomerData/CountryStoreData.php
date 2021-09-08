<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStore\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Opengento\CountryStore\Api\CountryRegistryInterface;
use Opengento\CountryStore\Api\CountryStoreResolverInterface;
use Opengento\CountryStore\Api\Data\CountryInterface;
use Opengento\CountryStore\Model\Resolver\DefaultCountryStore;
use Opengento\CountryStore\Model\Resolver\ResolverFactory;
use Psr\Log\LoggerInterface;

final class CountryStoreData implements SectionSourceInterface
{
    private CountryRegistryInterface $countryRegistry;

    private ResolverFactory $countryResolverFactory;

    private CountryStoreResolverInterface $countryStoreResolver;

    private StoreManagerInterface $storeManager;

    private ExtensibleDataObjectConverter $dataObjectConverter;

    private LoggerInterface $logger;

    public function __construct(
        CountryRegistryInterface $countryRegistry,
        ResolverFactory $countryResolverFactory,
        CountryStoreResolverInterface $countryStoreResolver,
        StoreManagerInterface $storeManager,
        ExtensibleDataObjectConverter $dataObjectConverter,
        LoggerInterface $logger
    ) {
        $this->countryRegistry = $countryRegistry;
        $this->countryResolverFactory = $countryResolverFactory;
        $this->countryStoreResolver = $countryStoreResolver;
        $this->storeManager = $storeManager;
        $this->dataObjectConverter = $dataObjectConverter;
        $this->logger = $logger;
    }

    public function getSectionData(): array
    {
        $country = $this->countryRegistry->get();

        if ($this->isInvalidated($country)) {
            $country = $this->countryResolverFactory->get(DefaultCountryStore::RESOLVER_CODE)->getCountry();
            $this->countryRegistry->set($country->getCode());
        }

        return $this->dataObjectConverter->toFlatArray($country);
    }

    private function isInvalidated(CountryInterface $country): bool
    {
        try {
            $registeredStore = $this->countryStoreResolver->getStoreAware($country);
            $currentStore = $this->storeManager->getStore();
        } catch (NoSuchEntityException $e) {
            $this->logger->error($e->getLogMessage(), $e->getTrace());

            return false;
        }

        return $registeredStore->getCode() !== $currentStore->getCode();
    }
}
