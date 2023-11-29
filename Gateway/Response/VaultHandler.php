<?php

namespace Mike\PaymentProcessor\Gateway\Response;

use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Payment\Model\InfoInterface;
use Magento\Payment\Model\Method\Logger;
use Magento\Sales\Api\Data\OrderPaymentExtensionInterface;
use Magento\Sales\Api\Data\OrderPaymentExtensionInterfaceFactory;
use Magento\Vault\Api\Data\PaymentTokenInterface;
use Magento\Vault\Model\PaymentTokenFactory;
use Laminas\Json\Encoder;

class VaultHandler implements HandlerInterface
{
    public const CUSTOMER_VAULT_ID = 'customer_vault_id';

    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    /**
     * @var PaymentTokenFactory
     */
    private $paymentTokenFactory;

    /**
     * @var OrderPaymentExtensionInterfaceFactory
     */
    private $paymentExtensionFactory;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * Constructor function
     *
     * @param PaymentTokenFactory $paymentTokenFactory
     * @param OrderPaymentExtensionInterfaceFactory $paymentExtensionFactory
     * @param Logger $logger
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        PaymentTokenFactory $paymentTokenFactory,
        OrderPaymentExtensionInterfaceFactory $paymentExtensionFactory,
        Logger $logger,
        EncryptorInterface $encryptor
    ) {
        $this->paymentTokenFactory = $paymentTokenFactory;
        $this->paymentExtensionFactory = $paymentExtensionFactory;
        $this->logger = $logger;
        $this->encryptor = $encryptor;
    }

    /**
     * @param array<string,mixed>|array<mixed> $handlingSubject
     * @param array<string,mixed>|array<mixed> $response
     * @return void
     */
    public function handle(array $handlingSubject, array $response)
    {
        if (!isset($handlingSubject['payment'])
            || !$handlingSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }
        $paymentDO = $handlingSubject['payment'];
        $payment = $paymentDO->getPayment();
        if (!$payment->getAdditionalInformation("is_active_payment_token_enabler")) {
            return;
        }
        if ($payment->getAdditionalInformation("cc_exp_month")) {
            $paymentToken = $this->getVaultPaymentToken($response, $payment);
            if (null !== $paymentToken) {
                $extensionAttributes = $this->getExtensionAttributes($payment);
                $extensionAttributes->setVaultPaymentToken($paymentToken);
            }
        }
        $this->logger->debug(['VaultHandler save card' => true]);
    }

    /**
     * Get vault payment token entity
     *
     * @param array<string> $response
     * @param InfoInterface $payment
     * @return PaymentTokenInterface|null
     */
    private function getVaultPaymentToken(array $response, InfoInterface $payment): ?PaymentTokenInterface
    {
        // Check token existing in gateway response
        if (isset($response[$this::CUSTOMER_VAULT_ID])) {
            $vaultId = $response[$this::CUSTOMER_VAULT_ID];
            if (empty($vaultId)) {
                return null;
            }
        } else {
            return null;
        }
        $ccExpMonth = $payment->getAdditionalInformation("cc_exp_month");
        $ccExpYear = $payment->getAdditionalInformation("cc_exp_year");
        $exp = sprintf('%02d/%02d', $ccExpMonth, $ccExpYear);
        $paymentToken = $this->paymentTokenFactory->create();
        $paymentToken->setType('card');
        $paymentToken->setGatewayToken($vaultId);
        $paymentToken->setExpiresAt($this->getExpirationDate($exp));
        $paymentToken->setPublicHash($this->generatePublicHash($paymentToken));
        $paymentToken->setTokenDetails($this->convertDetailsToJSON([
            'type' => $payment->getAdditionalInformation("cc_type"),
            'maskedCC' => substr($payment->getAdditionalInformation("cc_number"), -4),
            'expirationDate' => $exp
        ]));

        return $paymentToken;
    }

    /**
     * Generate vault payment public hash
     *
     * @param PaymentTokenInterface $paymentToken
     * @return string
     */
    public function generatePublicHash(PaymentTokenInterface $paymentToken)
    {
        $hashKey = $paymentToken->getGatewayToken();
        if ($paymentToken->getCustomerId()) {
            $hashKey = $paymentToken->getCustomerId();
        }

        $hashKey .= $paymentToken->getPaymentMethodCode()
            . $paymentToken->getType()
            . $paymentToken->getTokenDetails();

        return $this->encryptor->getHash($hashKey);
    }

    /**
     * GetExpirationDate function
     *
     * @param string $xExp
     * @return string
     */
    private function getExpirationDate(string $xExp)
    {
        $expDate = new \DateTime(
            '20' . substr($xExp, -2)
                . '-'
                . substr($xExp, 0, 2)
                . '-'
                . '01'
                . ' '
                . '00:00:00',
            new \DateTimeZone('UTC')
        );
        return $expDate->format('Y-m-d 00:00:00');
    }
    /**
     * Convert payment token details to JSON
     *
     * @param array<string> $details
     * @return string
     */
    private function convertDetailsToJSON($details)
    {
        $json = Encoder::encode($details);
        return $json ?: '{}';
    }

    /**
     * Get payment extension attributes
     *
     * @param InfoInterface $payment
     * @return OrderPaymentExtensionInterface
     */
    private function getExtensionAttributes(InfoInterface $payment): OrderPaymentExtensionInterface
    {
        $extensionAttributes = $payment->getExtensionAttributes();
        if (null === $extensionAttributes) {
            $extensionAttributes = $this->paymentExtensionFactory->create();
            $payment->setExtensionAttributes($extensionAttributes);
        }
        return $extensionAttributes;
    }
}
