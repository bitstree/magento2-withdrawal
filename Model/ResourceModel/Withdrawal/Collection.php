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

namespace BitsTree\Withdrawal\Model\ResourceModel\Withdrawal;

use BitsTree\Withdrawal\Model\Withdrawal;
use BitsTree\Withdrawal\Model\ResourceModel\Withdrawal as WithdrawalResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Withdrawal collection
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    protected function _construct(): void
    {
        $this->_init(Withdrawal::class, WithdrawalResource::class);
    }
}
