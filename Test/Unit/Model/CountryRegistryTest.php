<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStore\Test\Unit\Model;

use Magento\Framework\App\Request\DataPersistorInterface;
use Opengento\CountryStore\Api\CountryRegistryInterface;
use Opengento\CountryStore\Api\CountryRepositoryInterface;
use Opengento\CountryStore\Api\CountryResolverInterface;
use Opengento\CountryStore\Api\Data\CountryInterface;
use Opengento\CountryStore\Model\CountryRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Opengento\CountryStore\Model\CountryRegistry
 */
class CountryRegistryTest extends TestCase
{
    /**
     * @var MockObject|CountryRepositoryInterface
     */
    private $countryRepository;

    /**
     * @var MockObject|CountryResolverInterface
     */
    private $countryResolver;

    /**
     * @var MockObject|DataPersistorInterface
     */
    private $dataPersistor;

    private CountryRegistry $countryRegistry;

    protected function setUp(): void
    {
        $this->countryRepository = $this->getMockForAbstractClass(CountryRepositoryInterface::class);
        $this->countryResolver = $this->getMockForAbstractClass(CountryResolverInterface::class);
        $this->dataPersistor = $this->getMockForAbstractClass(DataPersistorInterface::class);

        $this->countryRegistry = new CountryRegistry(
            $this->countryResolver,
            $this->countryRepository,
            $this->dataPersistor
        );
    }

    /**
     * @dataProvider countryData
     */
    public function testGet(CountryInterface $country): void
    {
        $this->dataPersistor->expects($this->once())
            ->method('get')
            ->with(CountryRegistryInterface::PARAM_KEY)
            ->willReturn(null);

        $this->countryResolver->expects($this->once())->method('getCountry')->willReturn($country);
        $this->countryRepository->expects($this->once())->method('get')->willReturn($country);

        $this->assertSame($country->getCode(), $this->countryRegistry->get()->getCode());
        $this->assertSame($country->getName(), $this->countryRegistry->get()->getName());
    }

    /**
     * @dataProvider countryData
     */
    public function testSet(CountryInterface $country): void
    {
        $this->dataPersistor->expects($this->never())->method('get');
        $this->dataPersistor->expects($this->once())->method('set')
            ->with(CountryRegistryInterface::PARAM_KEY, $country->getCode());
        $this->countryResolver->expects($this->never())->method('getCountry');
        $this->countryRepository->expects($this->once())->method('get')->willReturn($country);

        $this->countryRegistry->set($country->getCode());

        $this->assertSame($country->getCode(), $this->countryRegistry->get()->getCode());
        $this->assertSame($country->getName(), $this->countryRegistry->get()->getName());
    }

    /**
     * @dataProvider countryData
     */
    public function testClear(CountryInterface $country): void
    {
        $this->countryRegistry->set('DE');
        $this->countryRegistry->clear();

        $this->dataPersistor->expects($this->once())->method('get')
            ->with(CountryRegistryInterface::PARAM_KEY)
            ->willReturn($country->getCode());
        $this->countryResolver->expects($this->never())->method('getCountry');
        $this->countryRepository->expects($this->once())->method('get')->willReturn($country);

        $this->assertNotEquals('DE', $this->countryRegistry->get()->getCode());
        $this->assertSame($country->getCode(), $this->countryRegistry->get()->getCode());
        $this->assertSame($country->getName(), $this->countryRegistry->get()->getName());
    }

    public function countryData(): array
    {
        return [
            [$this->createCountryMock('US', 'United States')],
            [$this->createCountryMock('FR', 'France')],
        ];
    }

    private function createCountryMock(string $code, string $name): MockObject
    {
        $countryMock = $this->getMockForAbstractClass(CountryInterface::class);
        $countryMock->method('getCode')->willReturn($code);
        $countryMock->method('getName')->willReturn($name);

        return $countryMock;
    }
}
