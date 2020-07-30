<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStore\Test\Unit\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Api\Data\GroupInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Model\StoreManagerInterface;
use Opengento\CountryStore\Api\Data\CountryInterface;
use Opengento\CountryStore\Model\Mapper\CountryStoreMapper;
use Opengento\CountryStore\Model\Store\GetStoreByCountry;
use Opengento\CountryStore\Model\Store\RelatedWebsites;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class GetStoreByCountryTest extends TestCase
{
    /**
     * @var MockObject|ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var MockObject|StoreManagerInterface
     */
    private $storeManager;

    private GetStoreByCountry $getStoreByCountry;

    protected function setUp(): void
    {
        $this->scopeConfig = $this->getMockForAbstractClass(ScopeConfigInterface::class);
        $this->storeManager = $this->getMockForAbstractClass(StoreManagerInterface::class);

        $this->getStoreByCountry = new GetStoreByCountry(
            $this->storeManager,
            new CountryStoreMapper(
                $this->scopeConfig,
                new Json(),
                $this->storeManager,
                $this->getMockForAbstractClass(LoggerInterface::class)
            ),
            new RelatedWebsites($this->scopeConfig, new Json()),
            $this->getMockForAbstractClass(LoggerInterface::class)
        );

        $this->setupStoreManager();
    }

    /**
     * @dataProvider mapperDataProvider
     */
    public function testGetByWebsite(CountryInterface $country, WebsiteInterface $website, string $storeCode): void
    {
        $this->assertSame($storeCode, $this->getStoreByCountry->getByWebsite($country, $website)->getCode());
    }

    public function mapperDataProvider(): array
    {
        return [
            [
                $this->createCountryMock('FR'), $this->createWebsiteMock(1, 'website_us', 11), 'store_emea_fr'
            ],
            [
                $this->createCountryMock('FR'), $this->createWebsiteMock(2, 'website_eu', 21), 'store_eu_fr'
            ],
            [
                $this->createCountryMock('FR'), $this->createWebsiteMock(3, 'website_emea', 31), 'store_emea_fr'
            ],
            [
                $this->createCountryMock('DE'), $this->createWebsiteMock(1, 'website_us', 11), 'store_us_us'
            ],
            [
                $this->createCountryMock('GB'), $this->createWebsiteMock(1, 'website_us', 11), 'store_us_us'
            ],
        ];
    }

    private function setupStoreManager(): void
    {
        $this->scopeConfig->method('getValue')->willReturnMap([
            ['country/information/website', 'default', null, '{"_0":{"websites":[1,3]},"_1":{"websites":[2]}}'],
            [
                'country/information/store',
                'default',
                null,
                '{"_0":{"countries":["US"],"store":111},"_1":{"countries":["CA"],"store":112},' .
                '"_2":{"countries":["FR"],"store":211},"_3":{"countries":["DE"],"store":212},' .
                '"_4":{"countries":["BE"],"store":213},"_5":{"countries":["RS"],"store":311},' .
                '"_6":{"countries":["RU"],"store":312},"_7":{"countries":["FR"],"store":313}}'
            ],
        ]);

        $this->storeManager->method('getWebsite')->willReturnMap([
            [1, $this->createWebsiteMock(1, 'website_us', 11)],
            [2, $this->createWebsiteMock(2, 'website_eu', 21)],
            [3, $this->createWebsiteMock(3, 'website_emea', 31)],
        ]);
        $this->storeManager->method('getStore')->willReturnMap([
            [111, $this->createStoreMock('store_us_us', 1)],
            [112, $this->createStoreMock('store_us_ca', 1)],
            [211, $this->createStoreMock('store_eu_fr', 2)],
            [212, $this->createStoreMock('store_eu_de', 2)],
            [213, $this->createStoreMock('store_eu_be', 2)],
            [311, $this->createStoreMock('store_emea_rs', 3)],
            [312, $this->createStoreMock('store_emea_ru', 3)],
            [313, $this->createStoreMock('store_emea_fr', 3)],
            ['store_us_us', $this->createStoreMock('store_us_us', 1)],
            ['store_us_ca', $this->createStoreMock('store_us_ca', 1)],
            ['store_eu_fr', $this->createStoreMock('store_eu_fr', 2)],
            ['store_eu_de', $this->createStoreMock('store_eu_de', 2)],
            ['store_eu_be', $this->createStoreMock('store_eu_be', 2)],
            ['store_emea_rs', $this->createStoreMock('store_emea_rs', 3)],
            ['store_emea_ru', $this->createStoreMock('store_emea_ru', 3)],
            ['store_emea_fr', $this->createStoreMock('store_emea_fr', 3)],
        ]);
        $this->storeManager->method('getGroup')->willReturnMap([
            [11, $this->createGroupMock('group_us', 111)],
            [21, $this->createGroupMock('group_eu', 211)],
            [31, $this->createGroupMock('group_emea', 312)],
        ]);
        $this->storeManager->method('getDefaultStoreView')->willReturn($this->createStoreMock('store_us_us', 1));
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

    private function createGroupMock(string $code, int $defaultStoreId): MockObject
    {
        $groupMock = $this->getMockForAbstractClass(GroupInterface::class);
        $groupMock->method('getCode')->willReturn($code);
        $groupMock->method('getDefaultStoreId')->willReturn($defaultStoreId);

        return $groupMock;
    }

    private function createWebsiteMock(int $id, string $code, int $defaultGroupId): MockObject
    {
        $websiteMock = $this->getMockForAbstractClass(WebsiteInterface::class);
        $websiteMock->method('getId')->willReturn($id);
        $websiteMock->method('getCode')->willReturn($code);
        $websiteMock->method('getDefaultGroupId')->willReturn($defaultGroupId);

        return $websiteMock;
    }
}
