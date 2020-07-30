<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStore\Model;

use Magento\Framework\App\Request\DataPersistorInterface;
use Opengento\CountryStore\Api\CountryRegistryInterface;
use Opengento\CountryStore\Api\CountryRepositoryInterface;
use Opengento\CountryStore\Api\CountryResolverInterface;
use Opengento\CountryStore\Api\Data\CountryInterface;

final class CountryRegistry implements CountryRegistryInterface
{
    private ?CountryInterface $country = null;

    private CountryResolverInterface $countryResolver;

    private CountryRepositoryInterface $countryRepository;

    private DataPersistorInterface $dataPersistor;

    public function __construct(
        CountryResolverInterface $countryResolver,
        CountryRepositoryInterface $countryRepository,
        DataPersistorInterface $dataPersistor
    ) {
        $this->countryResolver = $countryResolver;
        $this->countryRepository = $countryRepository;
        $this->dataPersistor = $dataPersistor;
    }

    public function get(): CountryInterface
    {
        if (!$this->country) {
            $this->set($this->dataPersistor->get(self::PARAM_KEY) ?: $this->countryResolver->getCountry()->getCode());
        }

        return $this->country;
    }

    public function set(string $countryCode): void
    {
        $this->dataPersistor->set(self::PARAM_KEY, $countryCode);
        $this->country = $this->countryRepository->get($countryCode);
    }

    public function clear(): void
    {
        $this->dataPersistor->clear(self::PARAM_KEY);
        $this->country = null;
    }
}
