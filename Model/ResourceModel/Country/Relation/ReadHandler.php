<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStore\Model\ResourceModel\Country\Relation;

use InvalidArgumentException;
use Magento\Directory\Model\Country;
use Magento\Directory\Model\ResourceModel\Country\CollectionFactory;
use Magento\Directory\Model\ResourceModel\Country\Collection;
use Magento\Framework\EntityManager\HydratorPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Opengento\CountryStore\Api\Data\CountryInterface;
use function is_array;

final class ReadHandler implements ExtensionInterface
{
    private HydratorPool $hydratorPool;

    private CollectionFactory $collectionFactory;

    private ?Collection $collection;

    public function __construct(
        HydratorPool $hydratorPool,
        CollectionFactory $collectionFactory
    ) {
        $this->hydratorPool = $hydratorPool;
        $this->collectionFactory = $collectionFactory;
        $this->collection = null;
    }

    /**
     * @inheridoc
     * @param CountryInterface $entity
     * @param array|null $arguments
     * @return CountryInterface
     * @throws NoSuchEntityException
     */
    public function execute($entity, $arguments = null): CountryInterface
    {
        if (!($entity instanceof CountryInterface)) {
            throw new InvalidArgumentException(
                'Argument name "entity" should be an instance of "' . CountryInterface::class . '".'
            );
        }
        if (!is_array($arguments) && !isset($arguments['code'])) {
            throw new InvalidArgumentException('Argument name "arguments" does not have "code" key-value pair.');
        }
        $country = $this->fetchCountry((string) $arguments['code']);

        return $this->hydratorPool->getHydrator(CountryInterface::class)->hydrate($entity, [
            'code' => $country->getCountryId(),
            'iso_alpha2' => $country->getData('iso2_code'),
            'iso_alpha3' => $country->getData('iso3_code'),
        ]);
    }

    /**
     * @param string $countryCode
     * @return Country
     * @throws NoSuchEntityException
     */
    private function fetchCountry(string $countryCode): Country
    {
        if ($this->collection === null) {
            $this->collection = $this->collectionFactory->create();
            $this->collection->addFieldToSelect(['country_id', 'iso2_code', 'iso3_code'])->load();
        }
        $country = $this->collection->getItemById($countryCode);
        if (!$country) {
            throw new NoSuchEntityException(new Phrase('There is no country with code "%1".', [$countryCode]));
        }

        return $country;
    }
}
