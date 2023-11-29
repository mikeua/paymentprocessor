<?php

namespace Mike\PaymentProcessor\Gateway\Request;

use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Model\Method\Logger;
use Magento\Sales\Model\Order\Payment;

class RefundRequest implements BuilderInterface
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * Constructor
     *
     * @param Logger $logger
     */
    public function __construct(
        Logger $logger
    ) {
        $this->logger = $logger;
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

        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = $buildSubject['payment'];

        $order = $paymentDO->getOrder();

        /** @var Payment $payment */
        $payment = $paymentDO->getPayment();

        $command = "refund";
        $amount = sprintf('%.2F', $buildSubject['amount']);
        $this->logger->debug(['GrandTotalAmount' => $order->getGrandTotalAmount(), 'command' => $command]);
        return [
            'type' => $command,
            'amount'   => $amount,
            'transactionid' => $payment->getParentTransactionId(),
        ];
    }
}
