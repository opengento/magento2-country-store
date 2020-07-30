<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStore\Block\Adminhtml\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Framework\View\Element\Html\Select;
use Opengento\CountryStore\Block\Adminhtml\Config\Form\Field\Select\Website;

class WebsiteGroup extends AbstractFieldArray
{
    /**
     * @throws LocalizedException
     */
    public function getCountrySelectRenderer(): Select
    {
        if (!$this->hasData('website_select_renderer')) {
            $this->setData(
                'website_select_renderer',
                $this->getLayout()->createBlock(
                    Website::class,
                    '',
                    ['data' => ['is_render_to_js_template' => true, 'multiple' => true]]
                )
            );
        }

        return $this->getData('website_select_renderer');
    }

    /**
     * @throws LocalizedException
     */
    protected function _prepareToRender(): void
    {
        $this->addColumn(
            'websites',
            [
                'label' => new Phrase('Websites'),
                'class' => 'required-entry',
                'renderer' => $this->getCountrySelectRenderer(),
            ]
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = (new Phrase('Add Website Group'))->render();
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $extra = [];

        foreach ((array) $row->getData('websites') as $country) {
            $extra['option_' . $this->getCountrySelectRenderer()->calcOptionHash($country)] = 'selected="selected"';
        }

        $row->setData('option_extra_attrs', $extra);
    }
}
