<?php

namespace Mike\PaymentProcessor\Test\Unit\Model\Ui;

use Mike\PaymentProcessor\Gateway\Config\Config;
use Mike\PaymentProcessor\Model\Ui\ConfigProvider;
use Magento\Framework\Locale\ResolverInterface;

class ConfigProviderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var ResolverInterface
     */
    private $resolverInterface;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->config = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->resolverInterface = $this->getMockBuilder(ResolverInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->configProvider = new ConfigProvider($this->config, $this->resolverInterface);
    }

    /**
     * @return void
     */
    public function testGetConfig()
    {
        static::assertEquals(
            [
                'payment' => [
                    ConfigProvider::CODE => [
                        'isActive' => $this->config->isActive(),
                        'ccVaultCode' => 'paymentprocessor_cc_vault',
                        'action' => $this->config->getCCPaymentAction(),
                    ],
                ],
            ],
            $this->configProvider->getConfig()
        );
    }
}
