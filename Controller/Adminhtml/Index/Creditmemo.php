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

namespace BitsTree\Withdrawal\Controller\Adminhtml\Index;

use BitsTree\Withdrawal\Api\WithdrawalRepositoryInterface;
use BitsTree\Withdrawal\Service\CreditMemoService;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Admin controller for manual credit memo creation from withdrawal
 */
class Creditmemo extends Action
{
    public const ADMIN_RESOURCE = 'BitsTree_Withdrawal::withdrawals';

    /**
     * @param Context $context
     * @param WithdrawalRepositoryInterface $withdrawalRepository
     * @param OrderRepositoryInterface $orderRepository
     * @param CreditMemoService $creditMemoService
     */
    public function __construct(
        Context $context,
        private readonly WithdrawalRepositoryInterface $withdrawalRepository,
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly CreditMemoService $creditMemoService,
    ) {
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $entityId = (int) $this->getRequest()->getParam('entity_id');

        try {
            $withdrawal = $this->withdrawalRepository->getById($entityId);
            $order = $this->orderRepository->get($withdrawal->getOrderId());
            $this->creditMemoService->createForWithdrawal($withdrawal, $order);
            $this->messageManager->addSuccessMessage(__('Credit memo has been created.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Could not create credit memo: %1', $e->getMessage()));
        }

        return $this->resultRedirectFactory->create()->setPath('bitstree_withdrawal/index/index');
    }
}
