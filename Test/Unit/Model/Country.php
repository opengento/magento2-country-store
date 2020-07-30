<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStore\Test\Unit\Model;

use Magento\Framework\Api\AbstractSimpleObject;
use Opengento\CountryStore\Api\Data\CountryExtension;
use Opengento\CountryStore\Api\Data\CountryExtensionInterface;
use Opengento\CountryStore\Api\Data\CountryInterface;

class Country extends AbstractSimpleObject implements CountryInterface
{
    /**
     * @inheritDoc
     */
    public function getCode(): string
    {
        return (string) $this->_get('code');
    }

    /**
     * @inheritDoc
     */
    public function getIsoAlpha2(): string
    {
        return (string) $this->_get('iso_alpha2');
    }

    /**
     * @inheritDoc
     */
    public function getIsoAlpha3(): string
    {
        return (string) $this->_get('iso_alpha3');
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->resolveName();
    }

    /**
     * @inheritDoc
     */
    public function getLocalizedName(string $locale): string
    {
        return $this->resolveName($locale);
    }

    /**
     * @inheritDoc
     */
    public function getExtensionAttributes(): CountryExtensionInterface
    {
        if (!$this->_get(self::EXTENSION_ATTRIBUTES_KEY)) {
            $this->setExtensionAttributes(new CountryExtension());
        }

        return $this->_get(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * @inheritDoc
     */
    public function setExtensionAttributes(CountryExtensionInterface $extensionAttributes): CountryInterface
    {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }

    private function resolveName(?string $locale = null): string
    {
        if (!$this->_get('name' . $locale)) {
            $this->setData('name' . $locale, $locale . $this->getName());
        }

        return (string) $this->_get('name' . $locale);
    }
}
