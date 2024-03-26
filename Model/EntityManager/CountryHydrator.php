<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStore\Model\EntityManager;

use InvalidArgumentException;
use Magento\Framework\Api\AbstractSimpleObject;
use Magento\Framework\EntityManager\HydratorInterface;
use Opengento\CountryStore\Api\Data\CountryInterface;

use function is_callable;
use function is_object;
use function method_exists;

final class CountryHydrator implements HydratorInterface
{
    public function extract($entity): array
    {
        if (!is_object($entity) || !is_callable([$entity, '__toArray']) || !method_exists($entity, '__toArray')) {
            throw new InvalidArgumentException('Argument name "entity" should implements "__toArray" method.');
        }

        return $entity->__toArray();
    }

    public function hydrate($entity, array $data): CountryInterface
    {
        if (!($entity instanceof CountryInterface)) {
            throw new InvalidArgumentException(
                'Argument name "entity" should be type of "' . CountryInterface::class . '".'
            );
        }
        if ($entity instanceof AbstractSimpleObject) {
            foreach ($data as $key => $value) {
                $entity->setData($key, $value);
            }
        }

        return $entity;
    }
}
