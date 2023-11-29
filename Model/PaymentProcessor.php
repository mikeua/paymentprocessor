<?php

namespace Mike\PaymentProcessor\Model;

use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;

class PaymentProcessor
{
    /**
     * Builds Sale request
     *
     * @param array<string,mixed>|array<mixed> $buildSubject
     * @param string $type
     * @return array<string>
     */
    public function buildSaleData(array $buildSubject, string $type): array
    {
        if (!isset($buildSubject['payment'])
            || !$buildSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        $paymentDO = $buildSubject['payment'];
        $amount = sprintf('%.2F', $buildSubject['amount']);
        $order = $paymentDO->getOrder();
        $payment = $paymentDO->getPayment();
        $cc_exp_month = $payment->getAdditionalInformation("cc_exp_month");
        $cc_exp_year = $payment->getAdditionalInformation("cc_exp_year");
        $vaultSave = $payment->getAdditionalInformation("is_active_payment_token_enabler");
        if (is_array($cc_exp_month) || is_array($cc_exp_year)) {
            throw new \LogicException('Credit card data invalid.');
        }
        return [
            'type' => $type,
            'amount' => $amount,
            'ccexp' => sprintf('%02d%02d', $cc_exp_month, substr($cc_exp_year, -2)),
            'cvv' => $payment->getAdditionalInformation("cc_cid"),
            'orderid' => $order->getOrderIncrementId(),
            'currency' => $order->getCurrencyCode(),
            'ccnumber' => $payment->getAdditionalInformation("cc_number"),
            'customer_vault' => $vaultSave ? 'add_customer' : ''
        ];
    }

    /**
     * Builds Vault Sale request
     *
     * @param array<string,mixed>|array<mixed> $buildSubject
     * @param string $type
     * @return array<string>
     */
    public function buildVaultSaleData(array $buildSubject, string $type): array
    {
        if (!isset($buildSubject['payment'])
            || !$buildSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        $paymentDO = $buildSubject['payment'];
        $amount = sprintf('%.2F', $buildSubject['amount']);
        $order = $paymentDO->getOrder();
        $payment = $paymentDO->getPayment();
        $extensionAttributes = $payment->getExtensionAttributes();
        $paymentToken = $extensionAttributes->getVaultPaymentToken();

        return [
            'type' => $type,
            'amount' => $amount,
            'orderid' => $order->getOrderIncrementId(),
            'currency' => $order->getCurrencyCode(),
            'customer_vault_id' => $paymentToken->getGatewayToken(),
        ];
    }
}
