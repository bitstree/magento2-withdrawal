<?php
/**
 * NOTICE OF LICENSE & COPYRIGHT
 *
 * Copyright (C) 2026 by BitsTree GmbH - All Rights Reserved
 * Unauthorized copying or editing of this file, via any medium is strictly prohibited.
 * Proprietary and confidential.
 *
 * @author     Robert Erlinger <robert.erlinger@bitstree.at>
 * @copyright  2026 BitsTree GmbH
 */
declare(strict_types=1);

namespace BitsTree\Withdrawal\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\BlockInterface;

/**
 * Dynamic rows for shipping method withdrawal period configuration
 */
class ShippingMethodPeriods extends AbstractFieldArray
{
    /**
     * @var BlockInterface|null
     */
    private ?BlockInterface $shippingMethodRenderer = null;

    protected function _prepareToRender(): void
    {
        $this->addColumn('shipping_method', [
            'label' => __('Shipping Method'),
            'renderer' => $this->getShippingMethodRenderer(),
        ]);
        $this->addColumn('period_days', [
            'label' => __('Period (Days)'),
            'class' => 'required-entry validate-digits validate-greater-than-zero',
        ]);
        $this->_addAfter = false;
        $this->_addButtonLabel = (string) __('Add');
    }

    protected function _prepareArrayRow(DataObject $row): void
    {
        $options = [];
        $shippingMethod = $row->getData('shipping_method');
        if ($shippingMethod !== null) {
            $key = 'option_' . $this->getShippingMethodRenderer()->calcOptionHash($shippingMethod);
            $options[$key] = 'selected="selected"';
        }
        $row->setData('option_extra_attrs', $options);
    }

    private function getShippingMethodRenderer(): BlockInterface
    {
        if ($this->shippingMethodRenderer === null) {
            $this->shippingMethodRenderer = $this->getLayout()->createBlock(
                ShippingMethodColumn::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }

        return $this->shippingMethodRenderer;
    }
}
