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

namespace BitsTree\Withdrawal\Api;

use BitsTree\Withdrawal\Api\Data\WithdrawalInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Withdrawal management service interface
 *
 * @api
 */
interface WithdrawalManagementInterface
{
    /**
     * Create a withdrawal for the given order
     *
     * @param int $orderId
     * @return \BitsTree\Withdrawal\Api\Data\WithdrawalInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function createByOrderId(int $orderId): WithdrawalInterface;

    /**
     * Create a withdrawal for the given order as a logged-in customer
     *
     * @param int $customerId
     * @param int $orderId
     * @return \BitsTree\Withdrawal\Api\Data\WithdrawalInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function createByOrderIdForCustomer(
        int $customerId,
        int $orderId,
    ): WithdrawalInterface;

    /**
     * Get orders eligible for withdrawal for the given customer
     *
     * @param int $customerId
     * @return \Magento\Sales\Api\Data\OrderInterface[]
     */
    public function getEligibleOrders(int $customerId): array;
}
