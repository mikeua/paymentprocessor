<?php

namespace Mike\PaymentProcessor\Gateway\Request;

use Mike\PaymentProcessor\Model\PaymentProcessor;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;

class VaultSaleRequest implements BuilderInterface
{
    /**
     * @var PaymentProcessor
     */
    private $paymentprocessor;

    /**
     * Constructor
     *
     * @param PaymentProcessor $paymentprocessor
     */
    public function __construct(
        PaymentProcessor $paymentprocessor
    ) {
        $this->paymentprocessor = $paymentprocessor;
    }

    /**
     * Builds ENV request
     *
     * @param array<string,mixed>|array<mixed> $buildSubject
     * @return array<string,mixed>|array<mixed>
     */
    public function build(array $buildSubject): array
    {
        if (!isset($buildSubject['payment'])
            || !$buildSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        $paymentDO = $buildSubject['payment'];
        $payment = $paymentDO->getPayment();

        if (!$payment instanceof OrderPaymentInterface) {
            throw new \LogicException('Order payment should be provided.');
        }
        $transactionId = $payment->getLastTransId();
        if (!$transactionId) {
            return $this->paymentprocessor->buildVaultSaleData($buildSubject, 'sale');
        }

        return [
            'type' => 'capture',
            'amount'   => sprintf('%.2F', $buildSubject['amount']),
            'transactionid' => $transactionId
        ];
    }
}
