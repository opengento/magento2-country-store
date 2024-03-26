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

use function strtoupper;

final class CountryRepository implements CountryRepositoryInterface
{
    /**
     * @var CountryInterface[]
     */
    private array $countries = [];

    public function __construct(
        private CountryInterfaceFactory $countryFactory,
        private ReadExtensions $readExtensions
    ) {}

    public function get(string $countryCode): CountryInterface
    {
        $countryCode = strtoupper($countryCode);

        return $this->countries[$countryCode] ??= $this->readExtensions->execute(
            $this->countryFactory->create(['data' => ['code' => $countryCode]]),
            ['code' => $countryCode]
        );
    }
}
