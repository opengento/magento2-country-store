<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStore\Model;

use Magento\Framework\EntityManager\Operation\Read\ReadExtensions;
use Opengento\CountryStore\Api\CountryRepositoryInterface;
use Opengento\CountryStore\Api\Data\CountryInterface;
use Opengento\CountryStore\Api\Data\CountryInterfaceFactory;

final class CountryRepository implements CountryRepositoryInterface
{
    private CountryInterfaceFactory $countryFactory;

    private ReadExtensions $readExtensions;

    /**
     * @var CountryInterface[]
     */
    private array $countries;

    public function __construct(
        CountryInterfaceFactory $countryFactory,
        ReadExtensions $readExtensions
    ) {
        $this->countryFactory = $countryFactory;
        $this->readExtensions = $readExtensions;
        $this->countries = [];
    }

    public function get(string $countryCode): CountryInterface
    {
        return $this->countries[$countryCode] ?? $this->countries[$countryCode] = $this->readExtensions->execute(
            $this->countryFactory->create(['data' => ['code' => $countryCode]]),
            ['code' => $countryCode]
        );
    }
}
