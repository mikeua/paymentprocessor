<?php

namespace Mike\PaymentProcessor\Model\Ui;

use Mike\PaymentProcessor\Gateway\Config\Config;
use Magento\Checkout\Model\ConfigProviderInterface;

class ConfigProvider implements ConfigProviderInterface
{
    public const CODE = 'paymentprocessor';
    public const CC_VAULT_CODE = 'paymentprocessor_cc_vault';

    /**
     * Config variable
     *
     * @var Config
     */
    private $config;

    /**
     * ConfigProvider function
     *
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * GetConfig function
     *
     * @return array<string,mixed>|array<mixed>
     */
    public function getConfig(): array
    {
        return [
            'payment' => [
                self::CODE => [
                    'isActive' => $this->config->isActive(),
                    'ccVaultCode' => self::CC_VAULT_CODE,
                    'action' => $this->config->getCCPaymentAction()
                ]
            ]
        ];
    }
}
