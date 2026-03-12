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
use BitsTree\Withdrawal\Api\WithdrawalManagementInterface;
use BitsTree\Withdrawal\Api\WithdrawalRepositoryInterface;
use BitsTree\Withdrawal\Helper\Config;
use BitsTree\Withdrawal\Model\Email\Sender;
use BitsTree\Withdrawal\Service\CreditMemoService;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Withdrawal management service implementation
 */
class WithdrawalManagement implements WithdrawalManagementInterface
{
    /**
     * Initialize dependencies
     *
     * @param OrderRepositoryInterface $orderRepository
     * @param WithdrawalRepositoryInterface $withdrawalRepository
     * @param WithdrawalFactory $withdrawalFactory
     * @param Config $config
     * @param Sender $emailSender
     * @param CreditMemoService $creditMemoService
     * @param Json $json
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly WithdrawalRepositoryInterface $withdrawalRepository,
        private readonly WithdrawalFactory $withdrawalFactory,
        private readonly Config $config,
        private readonly Sender $emailSender,
        private readonly CreditMemoService $creditMemoService,
        private readonly Json $json,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
    ) {
    }

    /**
     * @inheritdoc
     */
    public function createByOrderId(int $orderId): WithdrawalInterface
    {
        $order = $this->orderRepository->get($orderId);
        $storeId = (int) $order->getStoreId();

        if (!$this->config->isEnabled($storeId)) {
            throw new LocalizedException(__('Withdrawal is currently not available.'));
        }

        if (!$this->config->isWithdrawalAllowed($order, $storeId)) {
            throw new LocalizedException(__('The withdrawal period has expired.'));
        }

        $existingWithdrawal = $this->withdrawalRepository->getByOrderId($orderId);
        if ($existingWithdrawal) {
            throw new LocalizedException(__('A withdrawal request already exists for this order.'));
        }

        $eligibleItems = $this->config->getEligibleItems($order, $storeId);
        $itemsData = [];
        foreach ($eligibleItems as $item) {
            $itemsData[] = [
                'sku' => $item->getSku(),
                'name' => $item->getName(),
                'qty' => (float) $item->getQtyOrdered(),
            ];
        }

        $customerName = trim($order->getCustomerFirstname() . ' ' . $order->getCustomerLastname());

        $withdrawal = $this->withdrawalFactory->create();
        $withdrawal->setOrderId($orderId);
        $withdrawal->setOrderIncrementId($order->getIncrementId());
        $withdrawal->setCustomerId($order->getCustomerId() ? (int) $order->getCustomerId() : null);
        $withdrawal->setCustomerEmail($order->getCustomerEmail());
        $withdrawal->setCustomerName($customerName);
        $withdrawal->setShippingMethod($order->getShippingMethod());
        $withdrawal->setItemsData($this->json->serialize($itemsData));
        $withdrawal->setStatus(WithdrawalInterface::STATUS_PENDING);

        $this->withdrawalRepository->save($withdrawal);

        $order->addCommentToStatusHistory(__('Withdrawal requested'));
        $this->orderRepository->save($order);

        $this->emailSender->sendNotification($withdrawal, $order);

        if ($this->config->isAutoCreditmemoEnabled($storeId)) {
            $this->creditMemoService->createForWithdrawal($withdrawal, $order);
        }

        return $withdrawal;
    }

    /**
     * @inheritdoc
     */
    public function createByOrderIdForCustomer(
        int $customerId,
        int $orderId,
    ): WithdrawalInterface
    {
        $order = $this->orderRepository->get($orderId);

        if ((int) $order->getCustomerId() !== $customerId) {
            throw new AuthorizationException(__('Order not found.'));
        }

        return $this->createByOrderId($orderId);
    }

    /**
     * @inheritdoc
     */
    public function getEligibleOrders(int $customerId): array
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('customer_id', $customerId)
            ->create();

        $orders = $this->orderRepository->getList($searchCriteria)->getItems();
        $eligibleOrders = [];

        foreach ($orders as $order) {
            $storeId = (int) $order->getStoreId();

            if (!$this->config->isWithdrawalAllowed($order, $storeId)) {
                continue;
            }

            $existingWithdrawal = $this->withdrawalRepository->getByOrderId((int) $order->getEntityId());
            if ($existingWithdrawal) {
                continue;
            }

            $eligibleOrders[] = $order;
        }

        return $eligibleOrders;
    }
}