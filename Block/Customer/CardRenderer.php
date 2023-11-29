<?php

namespace Mike\PaymentProcessor\Block\Customer;

use Mike\PaymentProcessor\Model\Ui\ConfigProvider;
use Magento\Vault\Api\Data\PaymentTokenInterface;
use Magento\Vault\Block\AbstractCardRenderer;

class CardRenderer extends AbstractCardRenderer
{
    /**
     * Can render specified token
     *
     * @param PaymentTokenInterface $token
     * @return boolean
     */
    public function canRender(PaymentTokenInterface $token)
    {
        return $token->getPaymentMethodCode() === ConfigProvider::CODE;
    }

    /**
     * GetNumberLast4Digits function
     *
     * @return string
     */
    public function getNumberLast4Digits(): string
    {
        return ($this->getTokenDetails() !== null) ? $this->getTokenDetails()['maskedCC'] : '';
    }

    /**
     * GetExpDate function
     *
     * @return string
     */
    public function getExpDate()
    {
        return ($this->getTokenDetails() !== null) ? $this->getTokenDetails()['expirationDate'] : '';
    }

    /**
     * GetIconUrl function
     *
     * @return string
     */
    public function getIconUrl()
    {
        return ($this->getTokenDetails() !== null) ?
            $this->getIconForType($this->getTokenDetails()['type'])['url']
            : '';
    }

    /**
     * GetIconHeight function
     *
     * @return int
     */
    public function getIconHeight()
    {
        return ($this->getTokenDetails() !== null) ?
            $this->getIconForType($this->getTokenDetails()['type'])['height']
            : '';
    }

    /**
     * GetIconWidth function
     *
     * @return int
     */
    public function getIconWidth()
    {
        return ($this->getTokenDetails() !== null) ?
            $this->getIconForType($this->getTokenDetails()['type'])['width']
            : '';
    }
}
