<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStore\Model\Resolver;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\HTTP\PhpEnvironment\Request;
use Magento\Store\Model\ScopeInterface;
use Opengento\CountryStore\Api\CountryRepositoryInterface;
use Opengento\CountryStore\Api\CountryResolverInterface;
use Opengento\CountryStore\Api\Data\CountryInterface;
use Opengento\CountryStore\Model\CountryResolver;

final class HttpHeaderValue implements CountryResolverInterface
{
    public const RESOLVER_CODE = 'httpHeaderValue';

    private const CONFIG_PATH_COUNTRY_HTTP_HEADER_NAME = 'country/resolver/http_header_name';

    public function __construct(
        private Request $request,
        private ScopeConfigInterface $scopeConfig,
        private ResolverFactory $resolverFactory,
        private CountryRepositoryInterface $countryRepository
    ) {}

    public function getCountry(): CountryInterface
    {
        $countryCode = (string) $this->request->getServerValue(
            $this->scopeConfig->getValue(self::CONFIG_PATH_COUNTRY_HTTP_HEADER_NAME, ScopeInterface::SCOPE_WEBSITE)
        );

        return $countryCode
            ? $this->countryRepository->get($countryCode)
            : $this->resolverFactory->get(CountryResolver::DEFAULT_COUNTRY_RESOLVER_CODE)->getCountry();
    }
}
