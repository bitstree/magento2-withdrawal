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

namespace BitsTree\Withdrawal\Model;

use BitsTree\Withdrawal\Api\Data\WithdrawalInterface;
use BitsTree\Withdrawal\Api\WithdrawalRepositoryInterface;
use BitsTree\Withdrawal\Model\ResourceModel\Withdrawal as WithdrawalResource;
use BitsTree\Withdrawal\Model\ResourceModel\Withdrawal\CollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Withdrawal repository implementation
 */
class WithdrawalRepository implements WithdrawalRepositoryInterface
{
    public function __construct(
        private readonly WithdrawalResource $resource,
        private readonly WithdrawalFactory $withdrawalFactory,
        private readonly CollectionFactory $collectionFactory,
        private readonly CollectionProcessorInterface $collectionProcessor,
        private readonly SearchResultsInterfaceFactory $searchResultsFactory,
    ) {
    }

    /**
     * @inheritdoc
     */
    public function save(WithdrawalInterface $withdrawal): WithdrawalInterface
    {
        try {
            $this->resource->save($withdrawal);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save withdrawal: %1', $e->getMessage()), $e);
        }

        return $withdrawal;
    }

    /**
     * @inheritdoc
     */
    public function getById(int $entityId): WithdrawalInterface
    {
        $withdrawal = $this->withdrawalFactory->create();
        $this->resource->load($withdrawal, $entityId);

        if (!$withdrawal->getEntityId()) {
            throw new NoSuchEntityException(__('Withdrawal with ID "%1" does not exist.', $entityId));
        }

        return $withdrawal;
    }

    /**
     * @inheritdoc
     */
    public function getByOrderId(int $orderId): ?WithdrawalInterface
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('order_id', $orderId);
        $collection->setPageSize(1);

        $withdrawal = $collection->getFirstItem();

        return $withdrawal->getEntityId() ? $withdrawal : null;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface
    {
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }
}
