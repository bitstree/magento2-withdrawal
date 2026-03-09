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

/**
 * Source model for withdrawal period start date options
 */
class PeriodStartFrom implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'order_date', 'label' => __('Order Date')],
            ['value' => 'shipment_date', 'label' => __('Shipment Date')],
        ];
    }
}
