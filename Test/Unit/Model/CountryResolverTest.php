<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStore\Test\Unit\Model\Country;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\ObjectManagerInterface;
use Opengento\CountryStore\Api\CountryResolverInterface;
use Opengento\CountryStore\Api\Data\CountryInterface;
use Opengento\CountryStore\Model\CountryResolver;
use Opengento\CountryStore\Model\Resolver\ResolverFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Opengento\CountryStore\Model\CountryResolver
 */
class CountryResolverTest extends TestCase
{
    private MockObject|ScopeConfigInterface $scopeConfig;
    private MockObject|ObjectManagerInterface $objectFactory;
    private CountryResolver $countryResolver;

    /**
     * @var CountryInterface[]
     */
    private array $countries;

    protected function setUp(): void
    {
        $countryUs = $this->getMockForAbstractClass(CountryInterface::class);
        $countryFr = $this->getMockForAbstractClass(CountryInterface::class);

        $this->countries = [
            'US' => $countryUs,
            'FR' => $countryFr,
        ];

        $this->scopeConfig = $this->getMockForAbstractClass(ScopeConfigInterface::class);
        $this->objectFactory = $this->getMockForAbstractClass(ObjectManagerInterface::class);
        $resolverA = $this->getMockForAbstractClass(CountryResolverInterface::class);
        $resolverA->method('getCountry')->willReturn($countryUs);
        $resolverB = $this->getMockForAbstractClass(CountryResolverInterface::class);
        $resolverB->method('getCountry')->willReturn($countryFr);
        $this->objectFactory->method('get')->willReturnMap([
            ['Vendor\Module\Model\ResolverA', $resolverA],
            ['Vendor\Module\Model\ResolverA', $resolverA],
            ['Vendor\Module\Model\ResolverB', $resolverB],
        ]);

        $this->countryResolver = new CountryResolver(
            $this->scopeConfig,
            new ResolverFactory(
                $this->objectFactory,
                [
                    CountryResolver::DEFAULT_COUNTRY_RESOLVER_CODE => 'Vendor\Module\Model\ResolverA',
                    'resolverA' => 'Vendor\Module\Model\ResolverA',
                    'resolverB' => 'Vendor\Module\Model\ResolverB',
                ]
            )
        );
    }

    /**
     * @dataProvider resolverData
     */
    public function testGetCountry(string $countryCode, ?string $resolverCode): void
    {
        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with('country/resolver/code', 'website')
            ->willReturn($resolverCode);

        $this->assertSame($this->countries[$countryCode], $this->countryResolver->getCountry());
    }

    public function resolverData(): array
    {
        return [
            ['US', 'resolverA'],
            ['FR', 'resolverB'],
            ['US', null],
        ];
    }
}
