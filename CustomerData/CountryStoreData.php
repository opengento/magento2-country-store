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
        try {
            $store = $this->countryStoreResolver->getStoreAware($this->countryRegistry->get());
            $currentStore = $this->storeManager->getStore();

            if ($store->getCode() !== $currentStore->getCode()) {
                $this->countryRegistry->clear();
            }
        } catch (NoSuchEntityException $e) {
            $this->logger->error($e->getLogMessage(), $e->getTrace());
        }

        return $this->dataObjectConverter->toFlatArray($this->countryRegistry->get());
    }
}
