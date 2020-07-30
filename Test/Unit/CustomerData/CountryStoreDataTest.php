<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStore\Test\Unit\CustomerData;

use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Reflection\DataObjectProcessor;
use Opengento\CountryStore\Api\CountryRegistryInterface;
use Opengento\CountryStore\Api\Data\CountryExtensionInterface;
use Opengento\CountryStore\Api\Data\CountryInterface;
use Opengento\CountryStore\CustomerData\CountryStoreData;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Opengento\CountryStore\CustomerData\CountryStoreData
 */
class CountryStoreDataTest extends TestCase
{
    /**
     * @var MockObject|CountryRegistryInterface
     */
    private $countryRegistry;

    /**
     * @var MockObject|DataObjectProcessor
     */
    private $dataObjectProcessor;

    private ExtensibleDataObjectConverter $dataObjectConverter;

    private CountryStoreData $countryData;

    protected function setUp(): void
    {
        $this->countryRegistry = $this->getMockForAbstractClass(CountryRegistryInterface::class);
        $this->dataObjectProcessor = $this->createMock(DataObjectProcessor::class);
        $this->dataObjectConverter = new ExtensibleDataObjectConverter($this->dataObjectProcessor);

        $this->countryData = new CountryStoreData($this->countryRegistry, $this->dataObjectConverter);
    }

    /**
     * @dataProvider sectionData
     */
    public function testGetSectionData(CountryInterface $country, array $countryData, array $expected): void
    {
        $this->dataObjectProcessor->expects($this->once())
            ->method('buildOutputDataArray')
            ->with($country)
            ->willReturn($countryData);
        $this->countryRegistry->expects($this->once())->method('get')->willReturn($country);

        $this->assertSame($expected, $this->countryData->getSectionData());
    }

    public function sectionData(): array
    {
        $countryMockUs = $this->createCountryMock('US', 'United States', 'https://us.website.org/');
        $countryMockFr = $this->createCountryMock('FR', 'France', 'https://eu.website.org/');

        return [
            [
                $countryMockUs,
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
                $countryMockFr,
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
