<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStore\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * @api
 */
interface CountryInterface extends ExtensibleDataInterface
{
    /**
     * @return string
     */
    public function getCode(): string;

    /**
     * @return string
     */
    public function getIsoAlpha2(): string;

    /**
     * @return string
     */
    public function getIsoAlpha3(): string;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $locale
     * @return string
     */
    public function getLocalizedName(string $locale): string;

    /**
     * @return \Opengento\CountryStore\Api\Data\CountryExtensionInterface
     */
    public function getExtensionAttributes(): CountryExtensionInterface;

    /**
     * @param \Opengento\CountryStore\Api\Data\CountryExtensionInterface $extensionAttributes
     * @return \Opengento\CountryStore\Api\Data\CountryInterface
     */
    public function setExtensionAttributes(CountryExtensionInterface $extensionAttributes): CountryInterface;
}
