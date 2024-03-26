<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStore\Test\Unit\Model;

use Magento\Framework\EntityManager\Operation\Read\ReadExtensions;
use Opengento\CountryStore\Api\Data\CountryInterface;
use Opengento\CountryStore\Api\Data\CountryInterfaceFactory;
use Opengento\CountryStore\Model\CountryRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Opengento\CountryStore\Model\CountryRepository
 */
class CountryRepositoryTest extends TestCase
{
    private MockObject|CountryInterfaceFactory $countryFactory;
    private MockObject|ReadExtensions $readExtensions;
    private CountryRepository $countryRepository;

    protected function setUp(): void
    {
        $this->countryFactory = $this->createMock(CountryInterfaceFactory::class);
        $this->readExtensions = $this->createMock(ReadExtensions::class);

        $this->countryRepository = new CountryRepository($this->countryFactory, $this->readExtensions);
    }

    /**
     * @dataProvider countryData
     */
    public function testGet(string $countryCode): void
    {
        $country = $this->getMockForAbstractClass(CountryInterface::class);
        $country->method('getCode')->willReturn($countryCode);
        $this->countryFactory->expects($this->once())
            ->method('create')
            ->with(['data' => ['code' => $countryCode]])
            ->willReturn($country);
        $this->readExtensions->expects($this->once())
            ->method('execute')
            ->with($country, ['code' => $countryCode])
            ->willReturn($country);

        $this->assertSame($countryCode, $this->countryRepository->get($countryCode)->getCode());
    }

    public function countryData(): array
    {
        return [
            ['US'],
            ['FR'],
        ];
    }
}
