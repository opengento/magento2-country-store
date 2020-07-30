<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStore\Model\Resolver;

use Magento\Framework\ObjectManagerInterface;
use Opengento\CountryStore\Api\CountryResolverInterface;

/**
 * @api
 */
final class ResolverFactory
{
    private ObjectManagerInterface $objectManager;

    /**
     * @var string[]
     */
    private array $countryResolvers;

    public function __construct(
        ObjectManagerInterface $objectManager,
        array $countryResolvers = []
    ) {
        $this->objectManager = $objectManager;
        $this->countryResolvers = $countryResolvers;
    }

    public function get(string $resolverCode): CountryResolverInterface
    {
        return $this->objectManager->get($this->countryResolvers[$resolverCode]);
    }
}
