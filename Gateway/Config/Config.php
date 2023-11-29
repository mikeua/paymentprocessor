<?php

namespace Mike\PaymentProcessor\Gateway\Config;

class Config extends \Magento\Payment\Gateway\Config\Config
{
    public const KEY_ACTIVE = 'active';
    public const METHOD_CODE = 'paymentprocessor';
    public const API_KEY = "api_key";
    public const GATEWAY_URL = 'gateway_url';
    public const CC_PAYMENT_ACTION = "payment_action";

    /**
     * IsActive function
     *
     * @return boolean
     */
    public function isActive()
    {
        return (bool) $this->getValue(self::KEY_ACTIVE);
    }

    /**
     * getApiKey function
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->getValue(self::API_KEY);
    }

    /**
     * GetGatewayUrl function
     *
     * @return string
     */
    public function getGatewayUrl()
    {
        return $this->getValue(self::GATEWAY_URL);
    }

    /**
     * Get CC payment action function
     *
     * @return string
     */
    public function getCCPaymentAction()
    {
        return $this->getValue(self::CC_PAYMENT_ACTION);
    }
}
