<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStore\Model\ResourceModel\Country\Relation;

use InvalidArgumentException;
use Magento\Directory\Api\CountryInformationAcquirerInterface;
use Magento\Framework\EntityManager\HydratorPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Opengento\CountryStore\Api\Data\CountryInterface;
use Psr\Log\LoggerInterface;
use function is_array;

final class ReadHandler implements ExtensionInterface
{
    private CountryInformationAcquirerInterface $countryInfoAcquirer;

    private HydratorPool $hydratorPool;

    private LoggerInterface $logger;

    public function __construct(
        CountryInformationAcquirerInterface $countryInfoAcquirer,
        HydratorPool $hydratorPool,
        LoggerInterface $logger
    ) {
        $this->countryInfoAcquirer = $countryInfoAcquirer;
        $this->hydratorPool = $hydratorPool;
        $this->logger = $logger;
    }

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
        $countryCode = (string) $arguments['code'];

        try {
            $countryInformation = $this->countryInfoAcquirer->getCountryInfo($countryCode);
        } catch (NoSuchEntityException $e) {
            $this->logger->error($e->getLogMessage(), $e->getTrace());

            return $entity;
        }

        /** @var CountryInterface $entity */
        $entity = $this->hydratorPool->getHydrator(CountryInterface::class)->hydrate($entity, [
            'code' => $countryCode,
            'iso_alpha2' => $countryInformation->getTwoLetterAbbreviation(),
            'iso_alpha3' => $countryInformation->getThreeLetterAbbreviation(),
        ]);

        return $entity;
    }
}
