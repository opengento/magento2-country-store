<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStore\Test\Unit\Model\Config\Source;

use Opengento\CountryStore\Model\Config\Source\Options;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Opengento\CountryStore\Model\Config\Source\Options
 */
class OptionsTest extends TestCase
{
    /**
     * @dataProvider options
     */
    public function testToOptionArray(array $options): void
    {
        $this->assertSame($options, (new Options($options))->toOptionArray());
    }

    public function options(): array
    {
        return [
            [
                ['value' => 'defaultCountry', 'label' => 'Default Country'],
                ['value' => 'customResolver', 'label' => 'CustomResolver'],
            ],
        ];
    }
}
