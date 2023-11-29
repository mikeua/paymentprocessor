<?php

namespace Mike\PaymentProcessor\Gateway\Request;

use Mike\PaymentProcessor\Model\PaymentProcessor;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;

class AuthorizationRequest implements BuilderInterface
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
     * @return array<string>
     */
    public function build(array $buildSubject): array
    {
        return $this->paymentprocessor->buildSaleData($buildSubject, 'auth');
    }
}
