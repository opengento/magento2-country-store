<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStore\Test\Unit\Model\Resolver;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\HTTP\PhpEnvironment\Request;
use Magento\Framework\ObjectManagerInterface;
use Opengento\CountryStore\Api\CountryRepositoryInterface;
use Opengento\CountryStore\Api\CountryResolverInterface;
use Opengento\CountryStore\Api\Data\CountryInterface;
use Opengento\CountryStore\Model\CountryResolver;
use Opengento\CountryStore\Model\Resolver\HttpHeaderValue;
use Opengento\CountryStore\Model\Resolver\ResolverFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Opengento\CountryStore\Model\Resolver\HttpHeaderValue
 */
class HttpHeaderValueTest extends TestCase
{
    private MockObject|Request $request;
    private MockObject|ScopeConfigInterface $scopeConfig;
    private MockObject|CountryResolverInterface $resolver;
    private MockObject|CountryRepositoryInterface $countryRepository;
    private HttpHeaderValue $httpHeaderValueResolver;

    protected function setUp(): void
    {
        $this->request = $this->createMock(Request::class);
        $this->scopeConfig = $this->getMockForAbstractClass(ScopeConfigInterface::class);
        $this->resolver = $this->getMockForAbstractClass(CountryResolverInterface::class);
        $objectFactory = $this->getMockForAbstractClass(ObjectManagerInterface::class);
        $objectFactory->method('get')->willReturn($this->resolver);
        $this->countryRepository = $this->getMockForAbstractClass(CountryRepositoryInterface::class);
        $this->countryRepository->method('get')->willReturnMap([
            ['US', $this->createCountryMock('US')],
            ['FR', $this->createCountryMock('FR')]
        ]);

        $this->httpHeaderValueResolver = new HttpHeaderValue(
            $this->request,
            $this->scopeConfig,
            new ResolverFactory(
                $objectFactory,
                [CountryResolver::DEFAULT_COUNTRY_RESOLVER_CODE => 'Vendor\Module\Model\Resolver']
            ),
            $this->countryRepository
        );
    }

    /**
     * @dataProvider countryData
     */
    public function testGetCountryCode(string $countryCode, string $defaultCountryCode): void
    {
        $this->request->expects($this->exactly(2))
            ->method('getServerValue')
            ->with()
            ->willReturn($countryCode, null);
        $this->resolver->expects($this->once())
            ->method('getCountry')
            ->willReturn($this->createCountryMock($defaultCountryCode));

        $this->assertSame($countryCode, $this->httpHeaderValueResolver->getCountry()->getCode());
        $this->assertSame($defaultCountryCode, $this->httpHeaderValueResolver->getCountry()->getCode());
    }

    public function countryData(): array
    {
        return [
            ['US', 'US'],
            ['US', 'CA'],
        ];
    }

    private function createCountryMock(string $countryCode): MockObject
    {
        $countryMock = $this->getMockForAbstractClass(CountryInterface::class);
        $countryMock->method('getCode')->willReturn($countryCode);

        return $countryMock;
    }
}
