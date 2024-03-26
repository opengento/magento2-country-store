<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStore\Test\Unit\Model\Resolver;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Opengento\CountryStore\Api\CountryRepositoryInterface;
use Opengento\CountryStore\Api\Data\CountryInterface;
use Opengento\CountryStore\Model\Mapper\CountryStoreMapperInterface;
use Opengento\CountryStore\Model\Resolver\DefaultCountryStore;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Opengento\CountryStore\Model\Resolver\DefaultCountry
 */
class DefaultCountryStoreTest extends TestCase
{
    private MockObject|ScopeConfigInterface $scopeConfig;
    private MockObject|StoreManagerInterface $storeManager;
    private MockObject|CountryStoreMapperInterface $countryStoreMapper;
    private MockObject|CountryRepositoryInterface $countryRepository;
    private DefaultCountryStore $defaultCountryStore;

    protected function setUp(): void
    {
        $this->scopeConfig = $this->getMockForAbstractClass(ScopeConfigInterface::class);
        $this->storeManager = $this->createMock(StoreManagerInterface::class);
        $this->countryStoreMapper = $this->getMockForAbstractClass(CountryStoreMapperInterface::class);
        $this->countryRepository = $this->getMockForAbstractClass(CountryRepositoryInterface::class);

        $this->defaultCountryStore = new DefaultCountryStore(
            $this->scopeConfig,
            $this->storeManager,
            $this->countryStoreMapper,
            $this->countryRepository
        );
    }

    /**
     * @dataProvider countryData
     */
    public function testGetCountry(
        StoreInterface $store,
        array $countries,
        string $defaultCountryCode,
        CountryInterface $country
    ): void {
        $this->storeManager->method('getStore')->willReturn($store);
        $this->countryStoreMapper->expects($this->once())
            ->method('getCountriesByStore')
            ->with($store)
            ->willReturn($countries);
        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with('general/country/default', 'store')
            ->willReturn($defaultCountryCode);
        $this->countryRepository->expects($this->once())
            ->method('get')
            ->with($country->getCode())
            ->willReturn($country);

        $this->assertSame($country, $this->defaultCountryStore->getCountry());
    }

    public function countryData(): array
    {
        return [
            [
                $this->createStoreMock('store_us'),
                ['US'],
                'US',
                $this->createCountryMock('US'),
            ],
            [
                $this->createStoreMock('store_us'),
                ['CA'],
                'US',
                $this->createCountryMock('CA'),
            ],
            [
                $this->createStoreMock('store_eu'),
                ['DE','FR'],
                'FR',
                $this->createCountryMock('FR'),
            ],
        ];
    }

    private function createCountryMock(string $countryCode): MockObject
    {
        $countryMock = $this->getMockForAbstractClass(CountryInterface::class);
        $countryMock->method('getCode')->willReturn($countryCode);

        return $countryMock;
    }

    private function createStoreMock(string $storeCode): MockObject
    {
        $storeMock = $this->getMockForAbstractClass(StoreInterface::class);
        $storeMock->method('getCode')->willReturn($storeCode);

        return $storeMock;
    }
}
