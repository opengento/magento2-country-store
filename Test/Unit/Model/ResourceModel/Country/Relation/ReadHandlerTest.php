<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStore\Test\Unit\Model\ResourceModel\Country\Relation;

use Magento\Directory\Api\CountryInformationAcquirerInterface;
use Magento\Directory\Api\Data\CountryInformationInterface;
use Magento\Framework\EntityManager\HydratorPool;
use Opengento\CountryStore\Api\Data\CountryInterface;
use Opengento\CountryStore\Model\EntityManager\CountryHydrator;
use Opengento\CountryStore\Model\ResourceModel\Country\Relation\ReadHandler;
use Opengento\CountryStore\Test\Unit\Model\Country;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @covers \Opengento\CountryStore\Model\ResourceModel\Country\Relation\ReadHandler
 */
class ReadHandlerTest extends TestCase
{
    /**
     * @var MockObject|CountryInformationAcquirerInterface
     */
    private $countryInfoAcquirer;

    /**
     * @var MockObject|HydratorPool
     */
    private $hydratorPool;

    private ReadHandler $readHandler;

    protected function setUp(): void
    {
        $this->countryInfoAcquirer = $this->getMockForAbstractClass(CountryInformationAcquirerInterface::class);
        $this->hydratorPool = $this->getMockBuilder(HydratorPool::class)->disableOriginalConstructor()->getMock();

        $this->readHandler = new ReadHandler(
            $this->countryInfoAcquirer,
            $this->hydratorPool,
            $this->getMockForAbstractClass(LoggerInterface::class)
        );
    }

    /**
     * @dataProvider readHandlerData
     */
    public function testExecute(string $isoAlpha2, string $isoAlpha3): void
    {
        $country = new Country(['code' => $isoAlpha2]);
        $countryInfo = $this->getMockForAbstractClass(CountryInformationInterface::class);
        $countryInfo->expects($this->once())->method('getTwoLetterAbbreviation')->willReturn($isoAlpha2);
        $countryInfo->expects($this->once())->method('getThreeLetterAbbreviation')->willReturn($isoAlpha3);
        $this->countryInfoAcquirer->method('getCountryInfo')->with($country->getCode())->willReturn($countryInfo);
        $this->hydratorPool->expects($this->once())
            ->method('getHydrator')
            ->with(CountryInterface::class)
            ->willReturn(new CountryHydrator());

        /** @var CountryInterface $country */
        $country = $this->readHandler->execute($country, ['code' => $country->getCode()]);

        $this->assertSame($isoAlpha2, $country->getIsoAlpha2());
        $this->assertSame($isoAlpha3, $country->getIsoAlpha3());
    }

    public function readHandlerData(): array
    {
        return [
            ['FR', 'FRA'],
            ['US', 'USA'],
        ];
    }
}
