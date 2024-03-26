<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStore\Model\Resolver;

use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Phrase;
use Opengento\CountryStore\Api\CountryResolverInterface;

/**
 * @api
 */
final class ResolverFactory
{
    public function __construct(
        private ObjectManagerInterface $objectManager,
        private array $countryResolvers = []
    ) {}

    public function get(string $resolverCode): CountryResolverInterface
    {
        return $this->objectManager->get(
            $this->countryResolvers[$resolverCode] ?? throw new NotFoundException(
                new Phrase('"%1" resolver is not declared.', [$resolverCode])
            )
        );
    }
}
