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

namespace BitsTree\Withdrawal\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Shipping\Model\Config as ShippingConfig;

/**
 * Source model for active shipping methods
 */
class ShippingMethods implements OptionSourceInterface
{
    /**
     * @param ShippingConfig $shippingConfig
     */
    public function __construct(
        private readonly ShippingConfig $shippingConfig,
    ) {
    }

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        $options = [];
        $carriers = $this->shippingConfig->getActiveCarriers();

        foreach ($carriers as $carrierCode => $carrier) {
            $methods = $carrier->getAllowedMethods();
            if (!$methods) {
                continue;
            }
            $carrierTitle = $carrier->getConfigData('title') ?: $carrierCode;
            foreach ($methods as $methodCode => $methodTitle) {
                $value = $carrierCode . '_' . $methodCode;
                $label = $carrierTitle . ' - ' . ($methodTitle ?: $methodCode);
                $options[] = ['value' => $value, 'label' => $label];
            }
        }

        return $options;
    }
}
