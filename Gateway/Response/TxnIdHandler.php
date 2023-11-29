<?php

namespace Mike\PaymentProcessor\Gateway\Response;

use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Response\HandlerInterface;

class TxnIdHandler implements HandlerInterface
{
    public const RESP = 'response';
    public const RESP_TEXT = 'responsetext';
    public const AUTH_CODE = 'authcode';
    public const TX_ID = 'transactionid';
    public const AVS_RESP = 'avsresponse';
    public const CVV_RESP = 'cvvresponse';
    public const ORDER_ID = 'ordderid';
    public const TYPE = 'type';
    public const RESP_CODE = 'response_code';
    public const CUSTOMER_VAULT_ID = 'customer_vault_id';

    /**
     * AdditionalInformationMapping variable
     *
     * @var array<string>
     */
    private $additionalInformationMapping = [
        self::RESP,
        self::RESP_TEXT,
        self::AUTH_CODE,
        self::TX_ID,
        self::AVS_RESP,
        self::CVV_RESP,
        self::ORDER_ID,
        self::TYPE,
        self::RESP_CODE,
        self::CUSTOMER_VAULT_ID
    ];

    /**
     * Handles transaction id
     *
     * @param array<string,mixed>|array<mixed> $handlingSubject
     * @param array<string,mixed>|array<mixed> $response
     * @return void
     * @throws LocalizedException
     */
    public function handle(array $handlingSubject, array $response)
    {
        if (!isset($handlingSubject['payment'])
            || !$handlingSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = $handlingSubject['payment'];

        $payment = $paymentDO->getPayment();
        /** @var $payment \Magento\Sales\Model\Order\Payment */
        $payment->setTransactionId($response[$this::TX_ID]);
        $payment->setIsTransactionClosed(false);
        if (!$payment->getLastTransId()) {
            foreach ($this->additionalInformationMapping as $item) {
                if (!isset($response[$item])) {
                    continue;
                }
                $payment->setAdditionalInformation($item, $response[$item]);
            }
        }
    }
}
