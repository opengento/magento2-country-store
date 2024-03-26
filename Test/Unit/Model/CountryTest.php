<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStore\Test\Unit\Model;

use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\Locale\ListsInterface;
use Opengento\CountryStore\Api\Data\CountryExtensionInterface;
use Opengento\CountryStore\Model\Country;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Opengento\CountryStore\Model\Country
 */
class CountryTest extends TestCase
{
    private MockObject|ListsInterface $localeList;
    private MockObject|ExtensionAttributesFactory $extensionFactory;
    private Country $country;

    protected function setUp(): void
    {
        $this->localeList = $this->getMockForAbstractClass(ListsInterface::class);
        $this->extensionFactory = $this->createMock(ExtensionAttributesFactory::class);

        $this->country = new Country($this->extensionFactory, $this->localeList);
    }

    /**
     * @dataProvider countryIsoAlpha2
     */
    public function testGetCode(string $countryCode): void
    {
        $this->country->setData('code', $countryCode);

        $this->assertSame($countryCode, $this->country->getCode());
    }

    /**
     * @dataProvider countryIsoAlpha2
     */
    public function testGetIsoAlpha2(string $isoAlpha2): void
    {
        $this->country->setData('iso_alpha2', $isoAlpha2);

        $this->assertSame($isoAlpha2, $this->country->getIsoAlpha2());
    }

    /**
     * @dataProvider countryIsoAlpha3
     */
    public function testGetIsoAlpha3(string $isoAlpha3): void
    {
        $this->country->setData('iso_alpha3', $isoAlpha3);

        $this->assertSame($isoAlpha3, $this->country->getIsoAlpha3());
    }

    /**
     * @dataProvider countryNames
     */
    public function testGetName(string $name): void
    {
        $this->country->setData('name', $name);

        $this->assertSame($name, $this->country->getName());
    }

    /**
     * @dataProvider countryLocalizedNames
     */
    public function testGetLocaleName(string $countryCode, string $countryName, string $locale): void
    {
        $this->localeList->expects($this->once())
            ->method('getCountryTranslation')
            ->with($countryCode, $locale ?: null)
            ->willReturn($countryName);
        $this->country->setData('code', $countryCode);
        $this->country->setData('name', null);

        $this->assertSame($countryName, $this->country->getLocalizedName($locale));
        // Test that the country name resolution is done once
        $this->assertSame($countryName, $this->country->getLocalizedName($locale));
    }

    public function testGetExtensionAttributes(): void
    {
        $extensionAttributes = $this->getMockForAbstractClass(CountryExtensionInterface::class);
        $this->extensionFactory->expects($this->once())->method('create')->willReturn($extensionAttributes);

        $this->assertSame($extensionAttributes, $this->country->getExtensionAttributes());
    }

    public function testSetExtensionAttributes(): void
    {
        $extensionAttributes = $this->getMockForAbstractClass(CountryExtensionInterface::class);
        $this->extensionFactory->expects($this->once())->method('create')->willReturn($extensionAttributes);

        $this->country->setExtensionAttributes($this->country->getExtensionAttributes());

        $this->assertSame($extensionAttributes, $this->country->getExtensionAttributes());
    }

    public function countryIsoAlpha2(): array
    {
        return [['US'], ['FR']];
    }

    public function countryIsoAlpha3(): array
    {
        return [['USA'], ['FRA']];
    }

    public function countryNames(): array
    {
        return [['United States'], ['France']];
    }

    public function countryLocalizedNames(): array
    {
        return [['US', 'United States', 'en_US'], ['FR', 'France', 'en_US'], ['US', 'États-Unis', 'fr_FR']];
    }
}
