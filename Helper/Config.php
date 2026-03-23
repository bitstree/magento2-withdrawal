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

namespace BitsTree\Withdrawal\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Withdrawal configuration helper
 */
class Config extends AbstractHelper
{
    private const XML_PATH_PREFIX = 'bitstree_withdrawal/';

    /**
     * @param Context $context
     * @param Json $json
     */
    public function __construct(
        Context $context,
        private readonly Json $json,
    ) {
        parent::__construct($context);
    }

    /**
     * Check if withdrawal module is enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_PREFIX . 'general/enabled',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get company name for withdrawal texts
     *
     * @param int|null $storeId
     * @return string
     */
    public function getCompanyName(?int $storeId = null): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_PREFIX . 'general/company_name',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if button should be shown on order list
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isShowOnOrderList(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_PREFIX . 'general/show_on_order_list',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if button should be shown on order detail view
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isShowOnOrderView(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_PREFIX . 'general/show_on_order_view',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get SKU regex pattern for eligible items
     *
     * @param int|null $storeId
     * @return string
     */
    public function getSkuRegexPattern(?int $storeId = null): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_PREFIX . 'general/sku_regex',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if automatic credit memo creation is enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isAutoCreditmemoEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_PREFIX . 'general/auto_creditmemo',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get period start calculation method
     *
     * @param int|null $storeId
     * @return string
     */
    public function getPeriodStartFrom(?int $storeId = null): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_PREFIX . 'period/period_start_from',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get default withdrawal period in days
     *
     * @param int|null $storeId
     * @return int
     */
    public function getDefaultWithdrawalPeriodDays(?int $storeId = null): int
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_PREFIX . 'period/default_period',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $value ? (int) $value : 14;
    }

    /**
     * Get delivery date order attribute code
     *
     * @param int|null $storeId
     * @return string
     */
    public function getDeliveryDateAttribute(?int $storeId = null): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_PREFIX . 'period/delivery_date_attribute',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get delivery date withdrawal period in days
     *
     * @param int|null $storeId
     * @return int
     */
    public function getDeliveryDatePeriodDays(?int $storeId = null): int
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_PREFIX . 'period/delivery_date_period',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $value ? (int) $value : $this->getDefaultWithdrawalPeriodDays($storeId);
    }

    /**
     * Get shipping method code for "no delivery date" orders
     *
     * @param int|null $storeId
     * @return string
     */
    public function getNoDeliveryDateMethod(?int $storeId = null): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_PREFIX . 'period/no_delivery_date_method',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get withdrawal period in days for "no delivery date" orders
     *
     * @param int|null $storeId
     * @return int
     */
    public function getNoDeliveryDatePeriodDays(?int $storeId = null): int
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_PREFIX . 'period/no_delivery_date_period',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $value ? (int) $value : $this->getDefaultWithdrawalPeriodDays($storeId);
    }

    /**
     * Get withdrawal period for a specific shipping method
     *
     * @param string|null $shippingMethod
     * @param int|null $storeId
     * @return int
     */
    public function getWithdrawalPeriodForMethod(?string $shippingMethod, ?int $storeId = null, ?bool $returnDefaultWithdrawalPeriodDays = false): int
    {
        if ($shippingMethod === null) {
            return $this->getDefaultWithdrawalPeriodDays($storeId);
        }

        $rawValue = $this->scopeConfig->getValue(
            self::XML_PATH_PREFIX . 'period/shipping_method_periods',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if ($rawValue) {
            try {
                $periods = is_string($rawValue) ? $this->json->unserialize($rawValue) : $rawValue;
                if (is_array($periods)) {
                    foreach ($periods as $entry) {
                        if (isset($entry['shipping_method'], $entry['period_days'])
                            && $entry['shipping_method'] === $shippingMethod
                        ) {
                            return (int) $entry['period_days'];
                        }
                    }
                }
            } catch (\InvalidArgumentException $e) {
                // Invalid JSON, fall through to default
            }
        }

        if ($returnDefaultWithdrawalPeriodDays) {
            return $this->getDefaultWithdrawalPeriodDays($storeId);
        }
        return 0;
    }

    /**
     * Get customer email subject
     *
     * @param int|null $storeId
     * @return string
     */
    public function getCustomerEmailSubject(?int $storeId = null): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_PREFIX . 'email/customer_subject',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get notification email subject
     *
     * @param int|null $storeId
     * @return string
     */
    public function getNotifyEmailSubject(?int $storeId = null): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_PREFIX . 'email/notify_subject',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get notification email recipients
     *
     * @param int|null $storeId
     * @return string[]
     */
    public function getNotificationEmails(?int $storeId = null): array
    {
        $value = (string) $this->scopeConfig->getValue(
            self::XML_PATH_PREFIX . 'email/notification_emails',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if ($value === '') {
            return [];
        }

        return array_map('trim', explode(',', $value));
    }

    /**
     * Get email sender identity
     *
     * @param int|null $storeId
     * @return string
     */
    public function getEmailSender(?int $storeId = null): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_PREFIX . 'email/sender',
            ScopeInterface::SCOPE_STORE,
            $storeId
        ) ?: 'general';
    }

    /**
     * Get customer email template identifier
     *
     * @param int|null $storeId
     * @return string
     */
    public function getCustomerEmailTemplate(?int $storeId = null): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_PREFIX . 'email/customer_template',
            ScopeInterface::SCOPE_STORE,
            $storeId
        ) ?: 'bitstree_withdrawal_email_customer_template';
    }

    /**
     * Get store notification email template identifier
     *
     * @param int|null $storeId
     * @return string
     */
    public function getNotifyEmailTemplate(?int $storeId = null): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_PREFIX . 'email/notify_template',
            ScopeInterface::SCOPE_STORE,
            $storeId
        ) ?: 'bitstree_withdrawal_email_notify_template';
    }

    /**
     * Check if withdrawal is allowed for the given order
     *
     * @param OrderInterface $order
     * @param int|null $storeId
     * @return bool
     */
    public function isWithdrawalAllowed(OrderInterface $order, ?int $storeId = null): bool
    {
        if (!$this->isEnabled($storeId)) {
            return false;
        }

        if (!$this->hasEligibleItems($order, $storeId)) {
            return false;
        }

        return !$this->isWithdrawalPeriodExpired($order, $storeId);
    }

    /**
     * Check if the withdrawal period has expired for the given order
     *
     * @param OrderInterface $order
     * @param int|null $storeId
     * @return bool
     */
    public function isWithdrawalPeriodExpired(OrderInterface $order, ?int $storeId = null): bool
    {
        $noDeliveryDateMethod = $this->getNoDeliveryDateMethod($storeId);
        if ($noDeliveryDateMethod !== '' && $order->getShippingMethod() === $noDeliveryDateMethod) {
            $startDate = $order->getCreatedAt();
            $periodDays = $this->getNoDeliveryDatePeriodDays($storeId);

            if (!$startDate) {
                return false;
            }

            $deadline = new \DateTime($startDate);
            $deadline->modify('+' . $periodDays . ' days');

            return new \DateTime() > $deadline;
        }

        $deliveryDateAttribute = $this->getDeliveryDateAttribute($storeId);
        $deliveryDate = $deliveryDateAttribute !== '' ? $order->getData($deliveryDateAttribute) : null;

        if ($deliveryDate) {
            $startDate = $deliveryDate;
            $periodDays = $this->getWithdrawalPeriodForMethod($order->getShippingMethod(), $storeId, false);
            if ($periodDays === 0) {
                $periodDays = $this->getDeliveryDatePeriodDays($storeId);
            }
        } else {
            $startFrom = $this->getPeriodStartFrom($storeId);
            $periodDays = $this->getWithdrawalPeriodForMethod($order->getShippingMethod(), $storeId);

            if ($startFrom === 'shipment_date') {
                $shipmentsCollection = $order->getShipmentsCollection();
                if ($shipmentsCollection && $shipmentsCollection->getSize() > 0) {
                    $latestShipment = $shipmentsCollection->getLastItem();
                    $startDate = $latestShipment->getCreatedAt();
                } else {
                    // No shipment yet — withdrawal still allowed
                    return false;
                }
            } else {
                $startDate = $order->getCreatedAt();
            }
        }

        if (!$startDate) {
            return false;
        }

        $deadline = new \DateTime($startDate);
        $deadline->modify('+' . $periodDays . ' days');
        $now = new \DateTime();

        return $now > $deadline;
    }

    /**
     * Get order items eligible for withdrawal based on SKU regex
     *
     * @param OrderInterface $order
     * @param int|null $storeId
     * @return \Magento\Sales\Api\Data\OrderItemInterface[]
     */
    public function getEligibleItems(OrderInterface $order, ?int $storeId = null): array
    {
        $pattern = $this->getSkuRegexPattern($storeId);
        $items = [];

        foreach ($order->getAllVisibleItems() as $item) {
            if ($pattern === '' || preg_match('/' . $pattern . '/', $item->getSku())) {
                $items[] = $item;
            }
        }

        return $items;
    }

    /**
     * @param OrderInterface $order
     * @param int|null $storeId
     * @return bool
     */
    private function hasEligibleItems(OrderInterface $order, ?int $storeId = null): bool
    {
        return count($this->getEligibleItems($order, $storeId)) > 0;
    }
}
