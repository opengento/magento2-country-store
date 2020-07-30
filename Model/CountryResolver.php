<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStore\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Opengento\CountryStore\Api\CountryResolverInterface;
use Opengento\CountryStore\Api\Data\CountryInterface;
use Opengento\CountryStore\Model\Resolver\ResolverFactory;

final class CountryResolver implements CountryResolverInterface
{
    public const DEFAULT_COUNTRY_RESOLVER_CODE = 'defaultCountryStore';

    private const CONFIG_PATH_COUNTRY_STORE_RESOLVER_CODE = 'country/resolver/code';

    private ScopeConfigInterface $scopeConfig;

    private ResolverFactory $resolverFactory;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ResolverFactory $resolverFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->resolverFactory = $resolverFactory;
    }

    public function getCountry(): CountryInterface
    {
        return $this->resolverFactory->get($this->resolveCountryResolverCode())->getCountry();
    }

    private function resolveCountryResolverCode(): string
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_PATH_COUNTRY_STORE_RESOLVER_CODE,
            ScopeInterface::SCOPE_WEBSITE
        ) ?? self::DEFAULT_COUNTRY_RESOLVER_CODE;
    }
}
