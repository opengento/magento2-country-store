<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStore\Api;

use Opengento\CountryStore\Api\Data\CountryInterface;

/**
 * @api
 */
interface CountryRegistryInterface
{
    public const PARAM_KEY = '___country_code';

    public function get(): CountryInterface;

    public function set(string $countryCode): void;

    public function clear(): void;
}
