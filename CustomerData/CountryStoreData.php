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
use Psr\Log\LoggerInterface;

final class CountryStoreData implements SectionSourceInterface
{
    private CountryRegistryInterface $countryRegistry;

    private CountryStoreResolverInterface $countryStoreResolver;

    private StoreManagerInterface $storeManager;

    private ExtensibleDataObjectConverter $dataObjectConverter;

    private LoggerInterface $logger;

    public function __construct(
        CountryRegistryInterface $countryRegistry,
        CountryStoreResolverInterface $countryStoreResolver,
        StoreManagerInterface $storeManager,
        ExtensibleDataObjectConverter $dataObjectConverter,
        LoggerInterface $logger
    ) {
        $this->countryRegistry = $countryRegistry;
        $this->countryStoreResolver = $countryStoreResolver;
        $this->storeManager = $storeManager;
        $this->dataObjectConverter = $dataObjectConverter;
        $this->logger = $logger;
    }

    public function getSectionData(): array
    {
        $country = $this->countryRegistry->get();

        if ($this->isInvalidated($country)) {
            $this->countryRegistry->clear();
            $country = $this->countryRegistry->get();
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
