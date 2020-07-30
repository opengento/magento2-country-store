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
interface CountryRepositoryInterface
{
    public function get(string $countryCode): CountryInterface;
}
