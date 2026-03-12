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

namespace BitsTree\Withdrawal\Model\Email;

use BitsTree\Withdrawal\Api\Data\WithdrawalInterface;
use BitsTree\Withdrawal\Helper\Config;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Withdrawal email notification sender
 */
class Sender
{
    /**
     * @param TransportBuilder $transportBuilder
     * @param StateInterface $inlineTranslation
     * @param StoreManagerInterface $storeManager
     * @param Config $config
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly TransportBuilder $transportBuilder,
        private readonly StateInterface $inlineTranslation,
        private readonly StoreManagerInterface $storeManager,
        private readonly Config $config,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * Send withdrawal notification to customer and configured recipients
     *
     * @param WithdrawalInterface $withdrawal
     * @param OrderInterface $order
     * @return void
     */
    public function sendNotification(WithdrawalInterface $withdrawal, OrderInterface $order): void
    {
        $storeId = (int) $order->getStoreId();
        $templateVars = [
            'order_increment_id' => $withdrawal->getOrderIncrementId(),
            'customer_id' => $withdrawal->getCustomerId(),
            'customer_name' => $withdrawal->getCustomerName(),
            'customer_email' => $withdrawal->getCustomerEmail(),
            'items_data' => $withdrawal->getItemsData(),
            'withdrawal_date' => $withdrawal->getCreatedAt(),
            'store' => $this->storeManager->getStore($storeId),
            'company_name' => $this->config->getCompanyName($storeId),
        ];

        $sender = $this->config->getEmailSender($storeId);
        $subjectPlaceholders = [
            '{{order_id}}' => $withdrawal->getOrderIncrementId(),
            '{{customer_id}}' => (string) $withdrawal->getCustomerId(),
            '{{customer_name}}' => $withdrawal->getCustomerName(),
        ];

        $customerSubject = str_replace(
            array_keys($subjectPlaceholders),
            array_values($subjectPlaceholders),
            $this->config->getCustomerEmailSubject($storeId)
        );
        $customerVars = array_merge($templateVars, ['email_subject' => $customerSubject]);

        $this->sendEmail(
            $this->config->getCustomerEmailTemplate($storeId),
            $sender,
            $withdrawal->getCustomerEmail(),
            $withdrawal->getCustomerName(),
            $customerVars,
            $storeId
        );

        $notifySubject = str_replace(
            array_keys($subjectPlaceholders),
            array_values($subjectPlaceholders),
            $this->config->getNotifyEmailSubject($storeId)
        );
        $notifyVars = array_merge($templateVars, ['email_subject' => $notifySubject]);

        $notificationEmails = $this->config->getNotificationEmails($storeId);
        foreach ($notificationEmails as $email) {
            $this->sendEmail(
                $this->config->getNotifyEmailTemplate($storeId),
                $sender,
                $email,
                $email,
                $notifyVars,
                $storeId
            );
        }
    }

    /**
     * @param string $templateId
     * @param string $sender
     * @param string $recipientEmail
     * @param string $recipientName
     * @param array $templateVars
     * @param int $storeId
     * @return void
     */
    private function sendEmail(
        string $templateId,
        string $sender,
        string $recipientEmail,
        string $recipientName,
        array $templateVars,
        int $storeId,
    ): void {
        $this->inlineTranslation->suspend();

        try {
            $transport = $this->transportBuilder
                ->setTemplateIdentifier($templateId)
                ->setTemplateOptions([
                    'area' => 'frontend',
                    'store' => $storeId,
                ])
                ->setTemplateVars($templateVars)
                ->setFromByScope($sender, $storeId)
                ->addTo($recipientEmail, $recipientName)
                ->getTransport();

            $transport->sendMessage();
        } catch (\Exception $e) {
            $this->logger->error('BitsTree_Withdrawal email error: ' . $e->getMessage());
        } finally {
            $this->inlineTranslation->resume();
        }
    }
}
