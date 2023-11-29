<?php

namespace Mike\PaymentProcessor\Test\Unit\Gateway\Request;

use Mike\PaymentProcessor\Gateway\Request\RefundRequest;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Model\Method\Logger;
use Magento\Sales\Model\Order\Payment;
use PHPUnit\Framework\MockObject\MockObject;

class RefundRequestTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var ConfigInterface|MockObject
     */
    private $configMock;
    /**
     * @var OrderAdapterInterface|MockObject
     */
    private $orderMock;
    /**
     * @var PaymentDataObjectInterface|MockObject
     */
    private $paymentDO;
    /**
     * @var (Payment&MockObject)|MockObject
     */
    private $paymentModel;
    /**
     * @var (Data&MockObject)|MockObject
     */
    private $helper;
    /**
     * @var RefundRequest|MockObject
     */
    private $refundRequest;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->configMock = $this->createMock(ConfigInterface::class);
        $this->orderMock = $this->createMock(OrderAdapterInterface::class);
        $this->paymentDO = $this->createMock(PaymentDataObjectInterface::class);
        $this->paymentModel = $this->getMockBuilder(Payment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->helper = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->logger = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->refundRequest = new RefundRequest($this->logger, $this->helper);
    }

    /**
     * @return void
     */
    public function testBuild()
    {
        $amount = "10.00";
        $command = "refund";
        $txId = '23443535';

        $buildSubject = [
            'payment' => $this->paymentDO,
            'amount' => $amount
        ];

        $expectation = [
            'type' => $command,
            'amount'   => sprintf('%.2F', $amount),
            'transactionid' => null
        ];
        $this->paymentDO->expects(static::once())
            ->method('getPayment')
            ->willReturn($this->paymentModel);

        $this->paymentDO->expects(static::once())
            ->method('getOrder')
            ->willReturn($this->orderMock);

        static::assertEquals(
            $expectation,
            $this->refundRequest->build($buildSubject)
        );
    }

    /**
     * @return void
     */
    public function testBuildException()
    {
        $amount = '10.00';
        $buildSubject = [
            'payment' => null,
            'amount' => $amount
        ];
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Payment data object should be provided');
        $this->refundRequest->build($buildSubject);
    }
}
