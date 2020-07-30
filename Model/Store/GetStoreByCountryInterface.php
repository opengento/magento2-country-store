<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStore\Model\Store;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\Data\WebsiteInterface;
use Opengento\CountryStore\Api\Data\CountryInterface;

/**
 * @api
 */
interface GetStoreByCountryInterface
{
    /**
     * @param CountryInterface $country
     * @param WebsiteInterface $website
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    public function getByWebsite(CountryInterface $country, WebsiteInterface $website): StoreInterface;
}
