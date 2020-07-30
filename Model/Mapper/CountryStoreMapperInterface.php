<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStore\Model\Mapper;

use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\Data\WebsiteInterface;
use Opengento\CountryStore\Api\Data\CountryInterface;

/**
 * @api
 */
interface CountryStoreMapperInterface
{
    public function getCountriesByStore(StoreInterface $store): array;

    public function getStoresByCountry(CountryInterface $country, ?WebsiteInterface $website = null): array;
}
