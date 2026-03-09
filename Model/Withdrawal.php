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
use BitsTree\Withdrawal\Model\ResourceModel\Withdrawal as WithdrawalResource;
use Magento\Framework\Model\AbstractModel;

/**
 * Withdrawal entity model
 */
class Withdrawal extends AbstractModel implements WithdrawalInterface
{
    protected function _construct(): void
    {
        $this->_init(WithdrawalResource::class);
    }

    public function getEntityId(): ?int
    {
        $id = $this->getData(self::ENTITY_ID);
        return $id !== null ? (int) $id : null;
    }

    public function setEntityId($entityId): self
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    public function getOrderId(): ?int
    {
        $id = $this->getData(self::ORDER_ID);
        return $id !== null ? (int) $id : null;
    }

    public function setOrderId(int $orderId): self
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    public function getOrderIncrementId(): ?string
    {
        return $this->getData(self::ORDER_INCREMENT_ID);
    }

    public function setOrderIncrementId(string $orderIncrementId): self
    {
        return $this->setData(self::ORDER_INCREMENT_ID, $orderIncrementId);
    }

    public function getCustomerId(): ?int
    {
        $id = $this->getData(self::CUSTOMER_ID);
        return $id !== null ? (int) $id : null;
    }

    public function setCustomerId(?int $customerId): self
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    public function getCustomerEmail(): ?string
    {
        return $this->getData(self::CUSTOMER_EMAIL);
    }

    public function setCustomerEmail(string $customerEmail): self
    {
        return $this->setData(self::CUSTOMER_EMAIL, $customerEmail);
    }

    public function getCustomerName(): ?string
    {
        return $this->getData(self::CUSTOMER_NAME);
    }

    public function setCustomerName(string $customerName): self
    {
        return $this->setData(self::CUSTOMER_NAME, $customerName);
    }

    public function getShippingMethod(): ?string
    {
        return $this->getData(self::SHIPPING_METHOD);
    }

    public function setShippingMethod(?string $shippingMethod): self
    {
        return $this->setData(self::SHIPPING_METHOD, $shippingMethod);
    }

    public function getItemsData(): ?string
    {
        return $this->getData(self::ITEMS_DATA);
    }

    public function setItemsData(?string $itemsData): self
    {
        return $this->setData(self::ITEMS_DATA, $itemsData);
    }

    public function getStatus(): ?string
    {
        return $this->getData(self::STATUS);
    }

    public function setStatus(string $status): self
    {
        return $this->setData(self::STATUS, $status);
    }

    public function getCreatedAt(): ?string
    {
        return $this->getData(self::CREATED_AT);
    }

    public function setCreatedAt(string $createdAt): self
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    public function getUpdatedAt(): ?string
    {
        return $this->getData(self::UPDATED_AT);
    }

    public function setUpdatedAt(string $updatedAt): self
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
}
