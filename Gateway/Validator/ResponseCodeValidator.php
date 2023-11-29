<?php

namespace Mike\PaymentProcessor\Gateway\Validator;

use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterface;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Magento\Payment\Model\Method\Logger;

class ResponseCodeValidator extends AbstractValidator
{
    public const RESULT_CODE = 'response_code';
    public const RESULT_TEXT = 'responsetext';
    public const DECLINE = '200';
    public const SUCCESS = '100';

    /**
     * Logger variable
     *
     * @var Logger
     */
    private $logger;

    /**
     * ResponseCodeValidator function
     *
     * @param Logger $logger
     * @param ResultInterfaceFactory $resultFactory
     */
    public function __construct(
        Logger $logger,
        ResultInterfaceFactory $resultFactory
    ) {
        parent::__construct($resultFactory);
        $this->logger = $logger;
    }

    /**
     * Performs validation of result code
     *
     * @param array<string,mixed>|array<mixed> $validationSubject
     * @return ResultInterface
     */
    public function validate(array $validationSubject)
    {
        if (!isset($validationSubject['response']) || !is_array($validationSubject['response'])) {
            throw new \InvalidArgumentException('Response does not exist');
        }

        $response = $validationSubject['response'];
        $this->logger->debug(['Successful Transaction' => $response[self::RESULT_CODE] === self::SUCCESS]);
        if ($response[self::RESULT_CODE] === self::SUCCESS) {
            return $this->createResult(true);
        } else {
            return $this->createResult(
                false,
                [$response[self::RESULT_TEXT]],
                [$response[self::RESULT_CODE]]
            );
        }
    }
}
