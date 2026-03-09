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

namespace BitsTree\Withdrawal\Ui\Component\Listing\Column;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Withdrawal status options for admin grid
 */
class Status implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'pending', 'label' => __('Pending')],
            ['value' => 'confirmed', 'label' => __('Confirmed')],
            ['value' => 'rejected', 'label' => __('Rejected')],
        ];
    }
}
