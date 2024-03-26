<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStore\Test\Unit\Model\Store;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Api\Data\WebsiteInterface;
use Opengento\CountryStore\Model\Store\RelatedWebsites;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RelatedWebsitesTest extends TestCase
{
    private MockObject|ScopeConfigInterface $scopeConfig;
    private RelatedWebsites $relatedWebsites;

    protected function setUp(): void
    {
        $this->scopeConfig = $this->getMockForAbstractClass(ScopeConfigInterface::class);

        $this->relatedWebsites = new RelatedWebsites($this->scopeConfig, new Json());
    }

    /**
     * @dataProvider websiteData
     */
    public function testGetList(string $config, array $websiteList, WebsiteInterface $website): void
    {
        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with('country/information/website', 'default', null)
            ->willReturn($config);

        $this->assertSame($websiteList, $this->relatedWebsites->getListIds($website));
    }

    public function websiteData(): array
    {
        return [
            [
                '{"_0":{"websites":["1","2","3"]},"_1":{"websites":["4"]}}',
                [1, 2, 3],
                $this->createWebsiteMock(1),
            ],
            [
                '{"_0":{"websites":["1","2","3"]},"_1":{"websites":["4"]}}',
                [4],
                $this->createWebsiteMock(4),
            ],
            [
                '{"_0":{"websites":["1","2","3"]},"_1":{"websites":["4"]}}',
                [5],
                $this->createWebsiteMock(5),
            ],
        ];
    }

    private function createWebsiteMock(int $websiteId): MockObject
    {
        $website = $this->getMockForAbstractClass(WebsiteInterface::class);
        $website->method('getId')->willReturn($websiteId);

        return $website;
    }
}
