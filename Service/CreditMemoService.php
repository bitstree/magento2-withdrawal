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

namespace BitsTree\Withdrawal\Service;

use BitsTree\Withdrawal\Api\Data\WithdrawalInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order\CreditmemoFactory;
use Magento\Sales\Api\CreditmemoManagementInterface;
use Psr\Log\LoggerInterface;

/**
 * Automatic credit memo creation for withdrawal requests
 */
class CreditMemoService
{
    /**
     * @param CreditmemoFactory $creditmemoFactory
     * @param CreditmemoManagementInterface $creditmemoManagement
     * @param Json $json
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly CreditmemoFactory $creditmemoFactory,
        private readonly CreditmemoManagementInterface $creditmemoManagement,
        private readonly Json $json,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * Create credit memo for eligible withdrawal items
     *
     * @param WithdrawalInterface $withdrawal
     * @param OrderInterface $order
     * @return void
     */
    public function createForWithdrawal(WithdrawalInterface $withdrawal, OrderInterface $order): void
    {
        try {
            $itemsData = $withdrawal->getItemsData();
            if (!$itemsData) {
                return;
            }

            $withdrawnItems = $this->json->unserialize($itemsData);
            $withdrawnSkus = [];
            foreach ($withdrawnItems as $item) {
                $withdrawnSkus[$item['sku']] = $item['qty'];
            }

            $creditmemoItems = [];
            foreach ($order->getAllItems() as $orderItem) {
                if (isset($withdrawnSkus[$orderItem->getSku()])) {
                    $qty = min($withdrawnSkus[$orderItem->getSku()], $orderItem->getQtyInvoiced());
                    if ($qty > 0) {
                        $creditmemoItems[$orderItem->getId()] = ['qty' => $qty];
                    }
                }
            }

            if (empty($creditmemoItems)) {
                return;
            }

            $creditmemo = $this->creditmemoFactory->createByOrder($order, [
                'items' => $creditmemoItems,
                'shipping_amount' => 0,
                'adjustment_positive' => 0,
                'adjustment_negative' => 0,
            ]);

            $creditmemo->addComment(__('Automatic credit memo for withdrawal request'));
            $this->creditmemoManagement->refund($creditmemo);
        } catch (\Exception $e) {
            $this->logger->error('BitsTree_Withdrawal credit memo error: ' . $e->getMessage());
        }
    }
}
