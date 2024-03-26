<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStore\Test\Unit\CustomerData;

use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Opengento\CountryStore\Api\CountryRegistryInterface;
use Opengento\CountryStore\Api\CountryResolverInterface;
use Opengento\CountryStore\Api\CountryStoreResolverInterface;
use Opengento\CountryStore\Api\Data\CountryExtensionInterface;
use Opengento\CountryStore\Api\Data\CountryInterface;
use Opengento\CountryStore\CustomerData\CountryStoreData;
use Opengento\CountryStore\Model\Resolver\DefaultCountryStore;
use Opengento\CountryStore\Model\Resolver\ResolverFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @covers \Opengento\CountryStore\CustomerData\CountryStoreData
 */
class CountryStoreDataTest extends TestCase
{
    private const DEFAULT_RESOLVER_CLASS = 'Vendor\\Module\\Resolver\\Default';

    private MockObject|CountryRegistryInterface $countryRegistry;
    private MockObject|ObjectManagerInterface $objectManager;
    private ResolverFactory $countryResolverFactory;
    private CountryResolverInterface $countryResolver;
    private MockObject|CountryStoreResolverInterface $countryStoreResolver;
    private MockObject|StoreManagerInterface $storeManager;
    private MockObject|DataObjectProcessor $dataObjectProcessor;
    private ExtensibleDataObjectConverter $dataObjectConverter;
    private CountryStoreData $countryData;

    protected function setUp(): void
    {
        $this->countryRegistry = $this->getMockForAbstractClass(CountryRegistryInterface::class);
        $this->objectManager = $this->getMockForAbstractClass(ObjectManagerInterface::class);
        $this->countryResolverFactory = new ResolverFactory(
            $this->objectManager,
            [DefaultCountryStore::RESOLVER_CODE => self::DEFAULT_RESOLVER_CLASS]
        );
        $this->countryResolver = $this->getMockForAbstractClass(CountryResolverInterface::class);
        $this->countryStoreResolver = $this->getMockForAbstractClass(CountryStoreResolverInterface::class);
        $this->storeManager = $this->getMockForAbstractClass(StoreManagerInterface::class);
        $this->dataObjectProcessor = $this->createMock(DataObjectProcessor::class);
        $this->dataObjectConverter = new ExtensibleDataObjectConverter($this->dataObjectProcessor);

        $this->countryData = new CountryStoreData(
            $this->countryRegistry,
            $this->countryResolverFactory,
            $this->countryStoreResolver,
            $this->storeManager,
            $this->dataObjectConverter,
            $this->getMockForAbstractClass(LoggerInterface::class)
        );
    }

    /**
     * @dataProvider sectionData
     */
    public function testGetSectionData(
        $registeredCountry,
        $resolvedCountry,
        $registeredStore,
        $currentStore,
        array $countryData,
        array $expected
    ): void {
        $countryInvalidated = $registeredCountry !== $resolvedCountry;

        $this->objectManager->expects($countryInvalidated ? $this->once() : $this->never())
            ->method('get')
            ->with(self::DEFAULT_RESOLVER_CLASS)
            ->willReturn($this->countryResolver);
        $this->countryResolver->expects($countryInvalidated ? $this->once() : $this->never())
            ->method('getCountry')
            ->willReturn($resolvedCountry);
        $this->countryRegistry->expects($this->once())
            ->method('get')
            ->willReturn($registeredCountry);
        $this->countryRegistry->expects($countryInvalidated ? $this->once() : $this->never())
            ->method('set')
            ->with($resolvedCountry->getCode());

        $this->countryStoreResolver->expects($this->once())
            ->method('getStoreAware')
            ->with($registeredCountry)
            ->willReturn($registeredStore);

        $this->storeManager->expects($this->once())->method('getStore')->willReturn($currentStore);

        $this->dataObjectProcessor->expects($this->once())
            ->method('buildOutputDataArray')
            ->with($resolvedCountry)
            ->willReturn($countryData);

        $this->assertSame($expected, $this->countryData->getSectionData());
    }

    public function sectionData(): array
    {
        $countryMockUs = $this->createCountryMock('US', 'United States', 'https://us.website.org/');
        $countryMockFr = $this->createCountryMock('FR', 'France', 'https://eu.website.org/');

        $storeFr = $this->createStoreMock('fr');
        $storeUs = $this->createStoreMock('us');

        return [
            [
                $countryMockUs,
                $countryMockUs,
                $storeUs,
                $storeUs,
                [
                    'code' => 'US',
                    'name' => 'United State',
                    'extension_attributes' => [
                        'base_url' => 'https://us.website.org/',
                    ]
                ],
                [
                    'code' => 'US',
                    'name' => 'United State',
                    'base_url' => 'https://us.website.org/',
                ],
            ],
            [
                $countryMockUs,
                $countryMockFr,
                $storeUs,
                $storeFr,
                [
                    'code' => 'FR',
                    'name' => 'France',
                    'extension_attributes' => [
                        'base_url' => 'https://fr.website.org/',
                    ]
                ],
                [
                    'code' => 'FR',
                    'name' => 'France',
                    'base_url' => 'https://fr.website.org/',
                ],
            ],
        ];
    }

    private function createStoreMock(string $code): MockObject
    {
        $store = $this->getMockForAbstractClass(StoreInterface::class);
        $store->method('getCode')->willReturn($code);

        return $store;
    }

    private function createCountryMock(string $code, string $name, string $baseUrl): MockObject
    {
        $extensionAttributes = $this->getMockForAbstractClass(CountryExtensionInterface::class);
        $extensionAttributes->method('getBaseUrl')->willReturn($baseUrl);
        $country = $this->getMockForAbstractClass(CountryInterface::class);
        $country->method('getName')->willReturn($name);
        $country->method('getCode')->willReturn($code);
        $country->method('getExtensionAttributes')->willReturn($extensionAttributes);

        return $country;
    }
}
