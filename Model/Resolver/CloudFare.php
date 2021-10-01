<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStore\Model\Resolver;

use Magento\Framework\HTTP\PhpEnvironment\Request;
use Opengento\CountryStore\Api\CountryRepositoryInterface;
use Opengento\CountryStore\Api\CountryResolverInterface;
use Opengento\CountryStore\Api\Data\CountryInterface;
use Opengento\CountryStore\Model\CountryResolver;

final class CloudFare implements CountryResolverInterface
{
    public const RESOLVER_CODE = 'cloudFare';

    public const CF_HTTP_HEADER_IPCOUNTRY = 'HTTP_CF_IPCOUNTRY';

    private Request $request;

    private ResolverFactory $resolverFactory;

    private CountryRepositoryInterface $countryRepository;

    public function __construct(
        Request $request,
        ResolverFactory $resolverFactory,
        CountryRepositoryInterface $countryRepository
    ) {
        $this->request = $request;
        $this->resolverFactory = $resolverFactory;
        $this->countryRepository = $countryRepository;
    }

    public function getCountry(): CountryInterface
    {
        $countryCode = (string) $this->request->getServerValue(self::CF_HTTP_HEADER_IPCOUNTRY);

        return $countryCode
            ? $this->countryRepository->get($countryCode)
            : $this->resolverFactory->get(CountryResolver::DEFAULT_COUNTRY_RESOLVER_CODE)->getCountry();
    }
}
