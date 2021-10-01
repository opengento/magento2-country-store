<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStore\Test\Unit\Model\ResourceModel\Country\Relation;

use Magento\Directory\Model\Country as CountryModel;
use Magento\Directory\Model\ResourceModel\Country\Collection;
use Magento\Directory\Model\ResourceModel\Country\CollectionFactory;
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
     * @var MockObject|Collection
     */
    private $collectionMock;

    /**
     * @var MockObject|HydratorPool
     */
    private $hydratorPool;

    private ReadHandler $readHandler;

    protected function setUp(): void
    {
        $this->collectionMock = $this->getMockBuilder(Collection::class)->disableOriginalConstructor()->getMock();
        /** @var MockObject|CollectionFactory $collecFactoryMock */
        $collecFactoryMock = $this->getMockBuilder(CollectionFactory::class)->disableOriginalConstructor()->getMock();
        $collecFactoryMock->method('create')->willReturn($this->collectionMock);
        $this->hydratorPool = $this->getMockBuilder(HydratorPool::class)->disableOriginalConstructor()->getMock();
        $loggerMock = $this->getMockForAbstractClass(LoggerInterface::class);

        $this->readHandler = new ReadHandler($this->hydratorPool, $collecFactoryMock, $loggerMock);
    }

    /**
     * @dataProvider readHandlerData
     */
    public function testExecute(string $isoAlpha2, string $isoAlpha3): void
    {
        $country = new Country(['code' => $isoAlpha2]);

        $countryMock = $this->getMockBuilder(CountryModel::class)
            ->disableOriginalConstructor()
            ->setMethodsExcept(['getCountryId', '__call'])
            ->getMock();
        $countryMock->expects($this->exactly(3))->method('getData')->willReturnMap([
            ['country_id', null, $isoAlpha2],
            ['iso2_code', null, $isoAlpha2],
            ['iso3_code', null, $isoAlpha3],
        ]);
        $this->collectionMock->expects($this->once())->method('addFieldToSelect')->willReturn($this->collectionMock);
        $this->collectionMock->expects($this->once())->method('getItemById')->with($isoAlpha2)->willReturn($countryMock);

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
