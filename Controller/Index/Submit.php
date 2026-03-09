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

use BitsTree\Withdrawal\Api\WithdrawalRepositoryInterface;
use BitsTree\Withdrawal\Helper\Config;
use BitsTree\Withdrawal\Model\Email\Sender;
use BitsTree\Withdrawal\Model\WithdrawalFactory;
use BitsTree\Withdrawal\Service\CreditMemoService;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Frontend withdrawal submission controller
 */
class Submit implements HttpPostActionInterface
{
    /**
     * @param RequestInterface $request
     * @param RedirectFactory $redirectFactory
     * @param FormKeyValidator $formKeyValidator
     * @param CustomerSession $customerSession
     * @param OrderRepositoryInterface $orderRepository
     * @param WithdrawalRepositoryInterface $withdrawalRepository
     * @param WithdrawalFactory $withdrawalFactory
     * @param Config $config
     * @param Sender $emailSender
     * @param CreditMemoService $creditMemoService
     * @param ManagerInterface $messageManager
     * @param Json $json
     */
    public function __construct(
        private readonly RequestInterface $request,
        private readonly RedirectFactory $redirectFactory,
        private readonly FormKeyValidator $formKeyValidator,
        private readonly CustomerSession $customerSession,
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly WithdrawalRepositoryInterface $withdrawalRepository,
        private readonly WithdrawalFactory $withdrawalFactory,
        private readonly Config $config,
        private readonly Sender $emailSender,
        private readonly CreditMemoService $creditMemoService,
        private readonly ManagerInterface $messageManager,
        private readonly Json $json,
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

        if (!$this->config->isEnabled()) {
            $this->messageManager->addErrorMessage(__('Withdrawal is currently not available.'));
            return $redirect->setPath('sales/order/history');
        }

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

        $storeId = (int) $order->getStoreId();

        if (!$this->config->isWithdrawalAllowed($order, $storeId)) {
            $this->messageManager->addErrorMessage(__('The withdrawal period has expired.'));
            return $redirect->setPath('sales/order/view', ['order_id' => $orderId]);
        }

        $existingWithdrawal = $this->withdrawalRepository->getByOrderId($orderId);
        if ($existingWithdrawal) {
            $this->messageManager->addErrorMessage(__('A withdrawal request already exists for this order.'));
            return $redirect->setPath('sales/order/view', ['order_id' => $orderId]);
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
        $withdrawal->setCustomerId($customerId);
        $withdrawal->setCustomerEmail($order->getCustomerEmail());
        $withdrawal->setCustomerName($customerName);
        $withdrawal->setShippingMethod($order->getShippingMethod());
        $withdrawal->setItemsData($this->json->serialize($itemsData));
        $withdrawal->setStatus('pending');

        $this->withdrawalRepository->save($withdrawal);

        $order->addCommentToStatusHistory(__('Withdrawal requested'));
        $this->orderRepository->save($order);

        $this->emailSender->sendNotification($withdrawal, $order);

        if ($this->config->isAutoCreditmemoEnabled($storeId)) {
            $this->creditMemoService->createForWithdrawal($withdrawal, $order);
        }

        $this->messageManager->addSuccessMessage(
            __('Withdrawal has been sent via email, you will automatically receive a copy of the message.')
        );

        return $redirect->setPath('sales/order/view', ['order_id' => $orderId]);
    }
}
