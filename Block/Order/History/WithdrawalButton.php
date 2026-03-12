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

namespace BitsTree\Withdrawal\Block\Order\History;

use BitsTree\Withdrawal\Api\WithdrawalRepositoryInterface;
use BitsTree\Withdrawal\Helper\Config;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Withdrawal button block for order history list
 */
class WithdrawalButton extends Template
{
    /**
     * @var string
     */
    protected $_template = 'BitsTree_Withdrawal::order/withdrawal-button.phtml';

    /**
     * @param Context $context
     * @param Config $config
     * @param WithdrawalRepositoryInterface $withdrawalRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        private readonly Config $config,
        private readonly WithdrawalRepositoryInterface $withdrawalRepository,
        array $data = [],
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Check if withdrawal button should be displayed
     *
     * @param OrderInterface|null $order
     * @return bool
     */
    public function canShowButton(?OrderInterface $order = null): bool
    {
        $order = $order ?: $this->getOrder();
        if (!$order || !$order->getEntityId()) {
            return false;
        }

        $storeId = (int) $order->getStoreId();

        if (!$this->config->isEnabled($storeId)) {
            return false;
        }

        if (!$this->config->isShowOnOrderList($storeId)) {
            return false;
        }

        if ($this->withdrawalRepository->getByOrderId((int) $order->getEntityId())) {
            return false;
        }

        return $this->config->isWithdrawalAllowed($order, $storeId);
    }

    /**
     * @return OrderInterface|null
     */
    public function getOrder(): ?OrderInterface
    {
        return $this->getData('order');
    }

    /**
     * @param OrderInterface $order
     * @return string
     */
    public function getSubmitUrl(OrderInterface $order): string
    {
        return $this->getUrl('withdrawal/index/submit', ['order_id' => $order->getEntityId()]);
    }

    /**
     * @return string
     */
    public function getConfirmationMessage(): string
    {
        $companyName = $this->config->getCompanyName();

        return (string) __(
            'I would like to exercise my <strong>right of withdrawal.</strong><br/><strong>Note:</strong> The right of withdrawal only applies to accessories and the %1 oven <strong>(baked goods are excluded from withdrawal).</strong><br/>%1 may contact me to arrange the return.',
            $companyName,
        );
    }
}
