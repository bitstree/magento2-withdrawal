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

namespace BitsTree\Withdrawal\Api\Data;

/**
 * Withdrawal entity data interface
 *
 * @api
 */
interface WithdrawalInterface
{
    public const ENTITY_ID = 'entity_id';
    public const ORDER_ID = 'order_id';
    public const ORDER_INCREMENT_ID = 'order_increment_id';
    public const CUSTOMER_ID = 'customer_id';
    public const CUSTOMER_EMAIL = 'customer_email';
    public const CUSTOMER_NAME = 'customer_name';
    public const SHIPPING_METHOD = 'shipping_method';
    public const ITEMS_DATA = 'items_data';
    public const STATUS = 'status';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_REJECTED = 'rejected';

    /**
     * @return int|null
     */
    public function getEntityId(): ?int;

    /**
     * @param int $entityId
     * @return self
     */
    public function setEntityId(int $entityId): self;

    /**
     * @return int|null
     */
    public function getOrderId(): ?int;

    /**
     * @param int $orderId
     * @return self
     */
    public function setOrderId(int $orderId): self;

    /**
     * @return string|null
     */
    public function getOrderIncrementId(): ?string;

    /**
     * @param string $orderIncrementId
     * @return self
     */
    public function setOrderIncrementId(string $orderIncrementId): self;

    /**
     * @return int|null
     */
    public function getCustomerId(): ?int;

    /**
     * @param int|null $customerId
     * @return self
     */
    public function setCustomerId(?int $customerId): self;

    /**
     * @return string|null
     */
    public function getCustomerEmail(): ?string;

    /**
     * @param string $customerEmail
     * @return self
     */
    public function setCustomerEmail(string $customerEmail): self;

    /**
     * @return string|null
     */
    public function getCustomerName(): ?string;

    /**
     * @param string $customerName
     * @return self
     */
    public function setCustomerName(string $customerName): self;

    /**
     * @return string|null
     */
    public function getShippingMethod(): ?string;

    /**
     * @param string|null $shippingMethod
     * @return self
     */
    public function setShippingMethod(?string $shippingMethod): self;

    /**
     * @return string|null
     */
    public function getItemsData(): ?string;

    /**
     * @param string|null $itemsData
     * @return self
     */
    public function setItemsData(?string $itemsData): self;

    /**
     * @return string|null
     */
    public function getStatus(): ?string;

    /**
     * @param string $status
     * @return self
     */
    public function setStatus(string $status): self;

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string;

    /**
     * @param string $createdAt
     * @return self
     */
    public function setCreatedAt(string $createdAt): self;

    /**
     * @return string|null
     */
    public function getUpdatedAt(): ?string;

    /**
     * @param string $updatedAt
     * @return self
     */
    public function setUpdatedAt(string $updatedAt): self;
}

