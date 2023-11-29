<?php

namespace Mike\PaymentProcessor\Test\Unit\Gateway\Request;

use Mike\PaymentProcessor\Gateway\Request\VoidRequest;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Sales\Model\Order\Payment;
use PHPUnit\Framework\MockObject\MockObject;

class VoidRequestTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ConfigInterface|MockObject
     */
    private $configMock;
    /**
     * @var OrderAdapterInterface| MockObject
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
     * @var VoidRequest
     */
    private $voidRequest;
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

        /** @var ConfigInterface $configMock */
        $this->voidRequest = new VoidRequest($this->configMock);
    }

    /**
     * @return void
     */
    public function testBuild()
    {
        $txnId = 'fcd7f001e9274fdefb14bff91c799306';
        $storeId = 1;

        $expectation = [
            'type' => 'void',
            'transactionid' => null,
            'void_reason' => 'user_cancel'
        ];

        $this->paymentDO->expects(static::once())
            ->method('getPayment')
            ->willReturn($this->paymentModel);

        static::assertEquals(
            $expectation,
            $this->voidRequest->build(['payment' => $this->paymentDO])
        );
    }

    /**
     * @return void
     */
    public function testBuildException()
    {
        $buildSubject = [
            'payment' => null
        ];
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Payment data object should be provided');
        $this->voidRequest->build($buildSubject);
    }
}
