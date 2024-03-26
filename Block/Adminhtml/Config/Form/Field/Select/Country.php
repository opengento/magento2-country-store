<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStore\Block\Adminhtml\Config\Form\Field\Select;

use Magento\Directory\Model\Config\Source\Country\Full;
use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;

class Country extends Select
{
    public function __construct(
        Context $context,
        private Full $countrySource,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function setInputName(string $inputName): self
    {
        return $this->setData('name', $inputName);
    }

    protected function _toHtml(): string
    {
        if ($this->getData('multiple')) {
            $this->setData('extra_params', 'multiple="multiple"');
            $this->setInputName($this->getData('name') . '[]');
        }
        if (!$this->getOptions()) {
            $this->setOptions($this->countrySource->toOptionArray());
        }

        return parent::_toHtml();
    }
}
