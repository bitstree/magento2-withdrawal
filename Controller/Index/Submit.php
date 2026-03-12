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

namespace BitsTree\Withdrawal\Controller\Index;

use BitsTree\Withdrawal\Api\WithdrawalManagementInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Frontend withdrawal submission controller
 */
class Submit implements HttpPostActionInterface
{
    /**
     * Initialize dependencies
     *
     * @param RequestInterface $request
     * @param RedirectFactory $redirectFactory
     * @param FormKeyValidator $formKeyValidator
     * @param CustomerSession $customerSession
     * @param OrderRepositoryInterface $orderRepository
     * @param WithdrawalManagementInterface $withdrawalManagement
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        private readonly RequestInterface $request,
        private readonly RedirectFactory $redirectFactory,
        private readonly FormKeyValidator $formKeyValidator,
        private readonly CustomerSession $customerSession,
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly WithdrawalManagementInterface $withdrawalManagement,
        private readonly ManagerInterface $messageManager,
    ) {
    }

    /**
     * Process withdrawal submission
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $redirect = $this->redirectFactory->create();

        if (!$this->formKeyValidator->validate($this->request)) {
            $this->messageManager->addErrorMessage(__('Invalid form key. Please try again.'));

            return $redirect->setPath('sales/order/history');
        }

        $orderId = (int) $this->request->getParam('order_id');

        try {
            $order = $this->orderRepository->get($orderId);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Order not found.'));

            return $redirect->setPath('sales/order/history');
        }

        $customerId = (int) $this->customerSession->getCustomerId();
        if ((int) $order->getCustomerId() !== $customerId) {
            $this->messageManager->addErrorMessage(__('Order not found.'));

            return $redirect->setPath('sales/order/history');
        }

        try {
            $this->withdrawalManagement->createByOrderId($orderId);
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());

            return $redirect->setPath('sales/order/view', ['order_id' => $orderId]);
        }

        $this->messageManager->addSuccessMessage(
            __('Withdrawal has been sent via email, you will automatically receive a copy of the message.')
        );

        return $redirect->setPath('sales/order/view', ['order_id' => $orderId]);
    }
}