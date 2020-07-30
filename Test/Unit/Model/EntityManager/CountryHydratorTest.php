<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStore\Test\Unit\Model\EntityManager;

use Opengento\CountryStore\Model\EntityManager\CountryHydrator;
use Opengento\CountryStore\Test\Unit\Model\Country;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Opengento\CountryStore\Model\EntityManager\\CountryHydrator
 */
class CountryHydratorTest extends TestCase
{
    private CountryHydrator $countryHydrator;

    protected function setUp(): void
    {
        $this->countryHydrator = new CountryHydrator();
    }

    public function testExtract(): void
    {
        $country = new Country(['code' => 'FR']);
        $this->assertSame(['code' => 'FR'], $this->countryHydrator->extract($country));
    }

    public function testHydrate(): void
    {
        $country = new Country(['code' => 'FR']);
        $data = ['code' => 'US'];

        $this->assertSame($country, $this->countryHydrator->hydrate($country, $data));
    }
}
