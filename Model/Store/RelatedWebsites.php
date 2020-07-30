<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStore\Model\Store;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Api\Data\WebsiteInterface;
use function array_map;
use function array_merge;
use function in_array;

final class RelatedWebsites
{
    private const CONFIG_PATH_COUNTRY_WEBSITE_MAP = 'country/information/website';

    private ScopeConfigInterface $scopeConfig;

    private SerializerInterface $serializer;

    private array $websiteGroups;

    private array $websites;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        SerializerInterface $serializer
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
        $this->websites = [];
    }

    public function getListIds(WebsiteInterface $website): array
    {
        return $this->websites[(int) $website->getId()]
            ?? $this->websites[(int) $website->getId()] = $this->resolveWebsites($website);
    }

    private function resolveWebsites(WebsiteInterface $website): array
    {
        $websites = [[(int) $website->getId()]];

        foreach ($this->resolveWebsiteGroups() as $group) {
            $websitesIds = array_map('\intval', $group['websites'] ?? []);
            if (in_array((int) $website->getId(), $websitesIds, true)) {
                $websites[] = $websitesIds;
            }
        }

        return array_values(array_unique(array_merge(...$websites)));
    }

    private function resolveWebsiteGroups(): array
    {
        return $this->websiteGroups ?? $this->websiteGroups = $this->serializer->unserialize(
            $this->scopeConfig->getValue(self::CONFIG_PATH_COUNTRY_WEBSITE_MAP) ?? '{}'
        );
    }
}
