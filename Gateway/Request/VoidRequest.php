<?php

namespace Mike\PaymentProcessor\Gateway\Request;

use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Model\Order\Payment;

class VoidRequest implements BuilderInterface
{
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

        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = $buildSubject['payment'];

        /** @var Payment $payment */
        $payment = $paymentDO->getPayment();

        return [
            'type' => 'void',
            'transactionid' => $payment->getLastTransId(),
            'void_reason' => 'user_cancel'
        ];
    }
}
