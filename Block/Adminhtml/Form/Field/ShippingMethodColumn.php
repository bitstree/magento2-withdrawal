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

use BitsTree\Withdrawal\Model\Config\Source\ShippingMethods;
use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;

/**
 * Shipping method select column for dynamic rows
 */
class ShippingMethodColumn extends Select
{
    /**
     * @param Context $context
     * @param ShippingMethods $shippingMethods
     * @param array $data
     */
    public function __construct(
        Context $context,
        private readonly ShippingMethods $shippingMethods,
        array $data = [],
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @param string $value
     * @return self
     */
    public function setInputName(string $value): self
    {
        return $this->setName($value);
    }

    /**
     * @param string $value
     * @return self
     */
    public function setInputId(string $value): self
    {
        return $this->setId($value);
    }

    /**
     * @return string
     */
    public function _toHtml(): string
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->shippingMethods->toOptionArray());
        }

        return parent::_toHtml();
    }
}
