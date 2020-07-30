<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStore\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

final class Options implements OptionSourceInterface
{
    /**
     * @var string[][]
     */
    private array $options;

    public function __construct(
        array $options
    ) {
        $this->options = $options;
    }

    public function toOptionArray(): array
    {
        return $this->options;
    }
}
