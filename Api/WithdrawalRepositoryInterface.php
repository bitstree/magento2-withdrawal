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
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Withdrawal repository interface
 *
 * @api
 */
interface WithdrawalRepositoryInterface
{
    /**
     * Save a withdrawal entity
     *
     * @param WithdrawalInterface $withdrawal
     * @return WithdrawalInterface
     * @throws CouldNotSaveException
     */
    public function save(WithdrawalInterface $withdrawal): WithdrawalInterface;

    /**
     * Get withdrawal by ID
     *
     * @param int $entityId
     * @return WithdrawalInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $entityId): WithdrawalInterface;

    /**
     * Get withdrawal by order ID
     *
     * @param int $orderId
     * @return WithdrawalInterface|null
     */
    public function getByOrderId(int $orderId): ?WithdrawalInterface;

    /**
     * Get withdrawal list
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;
}
