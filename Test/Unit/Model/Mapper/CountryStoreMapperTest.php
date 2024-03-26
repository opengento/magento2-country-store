<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStore\Test\Unit\Model\Mapper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Opengento\CountryStore\Api\Data\CountryInterface;
use Opengento\CountryStore\Model\Mapper\CountryStoreMapper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @covers \Opengento\CountryStore\Model\Mapper\CountryStoreMapper
 */
class CountryStoreMapperTest extends TestCase
{
    private MockObject|ScopeConfigInterface $scopeConfig;
    private MockObject|StoreManagerInterface $storeRepository;
    private MockObject|StoreManagerInterface $websiteRepository;
    private CountryStoreMapper $countryStoreMapper;

    protected function setUp(): void
    {
        $this->scopeConfig = $this->getMockForAbstractClass(ScopeConfigInterface::class);
        $this->storeRepository = $this->getMockForAbstractClass(StoreRepositoryInterface::class);
        $this->websiteRepository = $this->getMockForAbstractClass(WebsiteRepositoryInterface::class);

        $this->countryStoreMapper = new CountryStoreMapper(
            $this->scopeConfig,
            new Json(),
            $this->storeRepository,
            $this->websiteRepository,
            $this->getMockForAbstractClass(LoggerInterface::class)
        );

        $this->setupStoreRepository();
    }

    /**
     * @dataProvider storesByCountryMapper
     */
    public function testGetStoresByCountry(CountryInterface $country, ?WebsiteInterface $website, array $stores): void
    {
        $this->assertSame($stores, $this->countryStoreMapper->getStoresByCountry($country, $website));
    }

    /**
     * @dataProvider countriesByStoreMapper
     */
    public function testGetCountriesByStore(StoreInterface $store, array $countries): void
    {
        $this->assertSame($countries, $this->countryStoreMapper->getCountriesByStore($store));
    }

    public function storesByCountryMapper(): array
    {
        return [
            [
                $this->createCountryMock('US'),
                null,
                ['store_us_us'],
            ],
            [
                $this->createCountryMock('US'),
                $this->createWebsiteMock(1, 'website_us'),
                ['store_us_us'],
            ],
            [
                $this->createCountryMock('US'),
                $this->createWebsiteMock(2, 'website_eu'),
                [],
            ],
            [
                $this->createCountryMock('FR'),
                null,
                ['store_eu_fr', 'store_emea_other'],
            ],
        ];
    }

    public function countriesByStoreMapper(): array
    {
        return [
            [
                $this->createStoreMock('store_us_us', 1),
                ['US'],
            ],
            [
                $this->createStoreMock('store_eu_fr', 2),
                ['FR'],
            ],
            [
                $this->createStoreMock('store_emea_other', 3),
                ['FR','ES']
            ]
        ];
    }

    private function setupStoreRepository(): void
    {
        $this->scopeConfig->method('getValue')
            ->with('country/information/store', 'default', null)
            ->willReturn(
                '{"_0":{"countries":["US"],"store":111},"_1":{"countries":["CA"],"store":112},' .
                '"_2":{"countries":["FR"],"store":211},"_3":{"countries":["DE"],"store":212},' .
                '"_4":{"countries":["BE"],"store":213},"_5":{"countries":["RS"],"store":311},' .
                '"_6":{"countries":["RU"],"store":312},"_7":{"countries":["FR","ES"],"store":313}}'
            );

        $this->websiteRepository->method('getById')->willReturnMap([
            [1, $this->createWebsiteMock(1, 'website_us')],
            [2, $this->createWebsiteMock(2, 'website_eu')],
            [3, $this->createWebsiteMock(3, 'website_emea')],
        ]);
        $this->storeRepository->method('getActiveStoreById')->willReturnMap([
            [111, $this->createStoreMock('store_us_us', 1)],
            [112, $this->createStoreMock('store_us_ca', 1)],
            [211, $this->createStoreMock('store_eu_fr', 2)],
            [212, $this->createStoreMock('store_eu_de', 2)],
            [213, $this->createStoreMock('store_eu_be', 2)],
            [311, $this->createStoreMock('store_emea_rs', 3)],
            [312, $this->createStoreMock('store_emea_ru', 3)],
            [313, $this->createStoreMock('store_emea_other', 3)],
        ]);
    }

    private function createCountryMock(string $code): MockObject
    {
        $countryMock = $this->getMockForAbstractClass(CountryInterface::class);
        $countryMock->method('getCode')->willReturn($code);

        return $countryMock;
    }

    private function createStoreMock(string $code, int $websiteId): MockObject
    {
        $storeMock = $this->getMockForAbstractClass(StoreInterface::class);
        $storeMock->method('getCode')->willReturn($code);
        $storeMock->method('getWebsiteId')->willReturn($websiteId);

        return $storeMock;
    }

    private function createWebsiteMock(int $id, string $code): MockObject
    {
        $websiteMock = $this->getMockForAbstractClass(WebsiteInterface::class);
        $websiteMock->method('getId')->willReturn($id);
        $websiteMock->method('getCode')->willReturn($code);

        return $websiteMock;
    }
}
