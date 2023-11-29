<?php

namespace Mike\PaymentProcessor\Test\Unit\Gateway\Response;

use Mike\PaymentProcessor\Gateway\Config\Config;
use Mike\PaymentProcessor\Gateway\Response\TxnIdHandler;
use Mike\PaymentProcessor\Test\Unit\Gateway\Response\MockObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Data\PaymentDataObject;
use Magento\Sales\Model\Order\Payment;
use Magento\Vault\Api\Data\PaymentTokenInterface;

class TxnIdHandlerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var string
     */
    private $paymentTokenFactory;

    /**
     * @var string
     */
    private $paymentExtensionFactory;
    /**
     * @var Config
     */
    private $config;

    /**
     * @var string
     */
    private $request;

    /**
     * @var \Magento\Sales\Api\Data\OrderPaymentExtension|MockObject
     */
    private $paymentExtension;

    /**
     * @var \Magento\Sales\Model\Order\Payment|MockObject
     */
    private $payment;

    /**
     * @var PaymentTokenInterface|MockObject
     */
    protected $paymentToken;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->paymentToken = $this->createMock(PaymentTokenInterface::class);

        $this->payment = $this->getMockBuilder(Payment::class)
            ->disableOriginalConstructor()
            ->setMethods(['__wakeup'])
            ->getMock();

        $this->request = new TxnIdHandler(
            $this->config
        );
    }

    /**
     * Test handle function
     *
     * @return void
     * @throws LocalizedException
     */
    public function testHandle()
    {

        $response = [
            TxnIdHandler::RESP => '1',
            TxnIdHandler::RESP_TEXT => 'SUCCESS',
            TxnIdHandler::AUTH_CODE => '100',
            TxnIdHandler::TX_ID => '123131323',
            TxnIdHandler::AVS_RESP => '',
            TxnIdHandler::CVV_RESP => '',
            TxnIdHandler::ORDER_ID => '767976867',
            TxnIdHandler::TYPE => 'auth',
            TxnIdHandler::RESP_CODE => '100',
            TxnIdHandler::CUSTOMER_VAULT_ID => '67868686'
        ];

        $paymentData = $this->getPaymentDataObjectMock();
        $subject = ['payment' => $paymentData];
        $this->request->handle($subject, $response);
    }

    /**
     * Create mock for payment data object and order payment
     * @return MockObject
     */
    private function getPaymentDataObjectMock()
    {
        $mock = $this->getMockBuilder(PaymentDataObject::class)
            ->setMethods(['getPayment'])
            ->disableOriginalConstructor()
            ->getMock();

        $mock->expects($this->once())
            ->method('getPayment')
            ->willReturn($this->payment);

        return $mock;
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    public function testHandleException()
    {
        $subject = [
            'payment' => null
        ];

        $response = [
            TxnIdHandler::RESP => '2',
            TxnIdHandler::RESP_TEXT => 'DECLINE',
            TxnIdHandler::AUTH_CODE => '200',
            TxnIdHandler::TX_ID => '123131323',
            TxnIdHandler::AVS_RESP => '',
            TxnIdHandler::CVV_RESP => '',
            TxnIdHandler::ORDER_ID => '767976867',
            TxnIdHandler::TYPE => 'auth',
            TxnIdHandler::RESP_CODE => '100',
            TxnIdHandler::CUSTOMER_VAULT_ID => '67868686'
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Payment data object should be provided');
        $this->request->handle($subject, $response);
    }
}
