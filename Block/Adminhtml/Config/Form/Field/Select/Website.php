<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStore\Block\Adminhtml\Config\Form\Field\Select;

use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;
use Magento\Store\Model\System\Store as StoreSource;

class Website extends Select
{
    public function __construct(
        Context $context,
        private StoreSource $storeSource,
        array $data = []
    ) {
        $this->storeSource = $storeSource;
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
            $this->setOptions($this->storeSource->getWebsiteValuesForForm());
        }

        return parent::_toHtml();
    }
}
