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

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Admin grid actions column
 */
class Actions extends Column
{
    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        private readonly UrlInterface $urlBuilder,
        array $components = [],
        array $data = [],
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['entity_id'])) {
                    $item[$this->getData('name')] = [
                        'view_order' => [
                            'href' => $this->urlBuilder->getUrl(
                                'sales/order/view',
                                ['order_id' => $item['order_id'] ?? 0]
                            ),
                            'label' => __('View Order'),
                        ],
                        'create_creditmemo' => [
                            'href' => $this->urlBuilder->getUrl(
                                'bitstree_withdrawal/index/creditmemo',
                                ['entity_id' => $item['entity_id']]
                            ),
                            'label' => __('Create Credit Memo'),
                            'confirm' => [
                                'title' => __('Create Credit Memo'),
                                'message' => __('Are you sure you want to create a credit memo for this withdrawal?'),
                            ],
                        ],
                    ];
                }
            }
        }

        return $dataSource;
    }
}
