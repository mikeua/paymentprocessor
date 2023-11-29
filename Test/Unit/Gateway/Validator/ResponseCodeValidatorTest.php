<?php

namespace Mike\PaymentProcessor\Test\Unit\Gateway\Validator;

use Mike\PaymentProcessor\Gateway\Validator\ResponseCodeValidator;
use Magento\Payment\Gateway\Validator\ResultInterface;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Magento\Payment\Model\Method\Logger;
use PHPUnit\Framework\MockObject\MockObject;

class ResponseCodeValidatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ResultInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultFactory;

    /**
     * @var ResultInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultMock;
    /**
     * @var Logger|(Logger&object&MockObject)|(Logger&MockObject)|(object&MockObject)|MockObject
     */
    private $mockLogger;
    /**
     * @var ResponseCodeValidator
     */
    private $validator;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->resultFactory = $this->getMockBuilder(
            'Magento\Payment\Gateway\Validator\ResultInterfaceFactory'
        )
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultMock = $this->createMock(ResultInterface::class);
        $this->mockLogger = $this->createMock(Logger::class);
        $this->validator = new ResponseCodeValidator($this->mockLogger, $this->resultFactory);
    }

    /**
     * @param array $response
     * @param array $expectationToResultCreation
     *
     * @dataProvider validateDataProvider
     */
    public function testValidate(array $response, array $expectationToResultCreation)
    {
        $this->resultFactory->expects(static::once())
            ->method('create')
            ->with(
                $expectationToResultCreation
            )
            ->willReturn($this->resultMock);

        static::assertInstanceOf(
            ResultInterface::class,
            $this->validator->validate(['response' => $response])
        );
    }

    /**
     * @return array[]
     */
    public function validateDataProvider()
    {
        return [
            'fail' => [
                'response' => [
                    ResponseCodeValidator::RESULT_CODE => ResponseCodeValidator::DECLINE,
                    ResponseCodeValidator::RESULT_TEXT => "DECLINE"

                ],
                'expectationToResultCreation' => [
                    'isValid' => false,
                    'failsDescription' => ["DECLINE"],
                    'errorCodes' => ["200"]
                ],
            ],
            'success' => [
                'response' => [
                    ResponseCodeValidator::RESULT_CODE => ResponseCodeValidator::SUCCESS,
                    ResponseCodeValidator::RESULT_TEXT => "SUCCESS",
                ],
                'expectationToResultCreation' => [
                    'isValid' => true,
                    'failsDescription' => [],
                    'errorCodes' => []
                ],
            ],
        ];
    }

    /**
     * @return void
     */
    public function testValidateException()
    {
        $buildSubject = [
            'response' => null,
        ];
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Response does not exist');
        $this->validator->validate($buildSubject);
    }
}
