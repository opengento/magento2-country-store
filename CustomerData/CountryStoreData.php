<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStore\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Opengento\CountryStore\Api\CountryRegistryInterface;

final class CountryStoreData implements SectionSourceInterface
{
    private CountryRegistryInterface $countryRegistry;

    private ExtensibleDataObjectConverter $dataObjectConverter;

    public function __construct(
        CountryRegistryInterface $countryRegistry,
        ExtensibleDataObjectConverter $dataObjectConverter
    ) {
        $this->countryRegistry = $countryRegistry;
        $this->dataObjectConverter = $dataObjectConverter;
    }

    public function getSectionData(): array
    {
        return $this->dataObjectConverter->toFlatArray($this->countryRegistry->get());
    }
}
