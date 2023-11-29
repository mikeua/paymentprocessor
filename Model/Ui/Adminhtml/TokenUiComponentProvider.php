<?php

namespace Mike\PaymentProcessor\Model\Ui\Adminhtml;

use Mike\PaymentProcessor\Gateway\Response\VaultHandler;
use Mike\PaymentProcessor\Model\Ui\ConfigProvider;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template;
use Magento\Vault\Api\Data\PaymentTokenInterface;
use Magento\Vault\Model\Ui\TokenUiComponentInterface;
use Magento\Vault\Model\Ui\TokenUiComponentInterfaceFactory;
use Magento\Vault\Model\Ui\TokenUiComponentProviderInterface;

/**
 * Class TokenProvider
 */
class TokenUiComponentProvider implements TokenUiComponentProviderInterface
{
    /**
     * @var TokenUiComponentInterfaceFactory
     */
    private $componentFactory;

    /**
     * @var VaultHandler
     */
    private $vaultHandler;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * TokenUiComponentProvider
     *
     * @param TokenUiComponentInterfaceFactory $componentFactory
     * @param VaultHandler $vaultHandler
     * @param Json $serializer
     */
    public function __construct(
        TokenUiComponentInterfaceFactory $componentFactory,
        VaultHandler $vaultHandler,
        Json $serializer
    ) {
        $this->componentFactory = $componentFactory;
        $this->vaultHandler     = $vaultHandler;
        $this->serializer       = $serializer;
    }

    /**
     * Get UI component for token
     *
     * @param PaymentTokenInterface $paymentToken
     * @return TokenUiComponentInterface
     * @throws \Exception
     */
    public function getComponentForToken(PaymentTokenInterface $paymentToken): TokenUiComponentInterface
    {
        $data = $this->serializer->unserialize($paymentToken->getTokenDetails() ?: '{}');
        $hash = $paymentToken->getPublicHash();
        if (!$hash) {
            $hash = $this->vaultHandler->generatePublicHash($paymentToken);
            $paymentToken->setPublicHash($hash);
        }
        return $this->componentFactory->create(
            [
                'config' => [
                    'code' => ConfigProvider::CC_VAULT_CODE,
                    TokenUiComponentProviderInterface::COMPONENT_DETAILS => $data,
                    TokenUiComponentProviderInterface::COMPONENT_PUBLIC_HASH => $paymentToken->getPublicHash(),
                    'template' => 'Mike_PaymentProcessor::form/vault.phtml'
                ],
                'name' => Template::class
            ]
        );
    }
}
