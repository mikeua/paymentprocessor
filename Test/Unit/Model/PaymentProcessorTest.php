<?php

namespace Mike\PaymentProcessor\Test\Unit\Model;

use Mike\PaymentProcessor\Gateway\Config\Config;
use Mike\PaymentProcessor\Gateway\Request\AuthorizationRequest;
use Mike\PaymentProcessor\Model\PaymentProcessor;
use Mike\PaymentProcessor\Test\Unit\Gateway\Request\Data;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Sales\Model\Order\Payment;
use PHPUnit\Framework\MockObject\MockObject;

class PaymentProcessorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Payment
     */
    private $payment;

    /**
     * @var PaymentDataObjectInterface
     */
    private $paymentDO;

    /**
     *
     * @var AuthorizationRequest
     */
    private $authorizationRequest;

    /**
     * @var OrderAdapterInterface
     */
    private $order;

    /**
     *
     * @var Config
     */
    private $config;

    public const CC_TYPE = 'VI';
    public const CC_NUMBER = '4sdfssdfsdfdsf1111';
    public const CVV = "ewerwre2345";
    public const CC_EXP_MONTH = 10;
    public const CC_EXP_YEAR = 2028;
    /**
     * @var (Data&MockObject)|MockObject
     */
    private $helper;
    /**
     * @var PaymentProcessor
     */
    private $paymentprocessor;
    /**
     * @var PaymentProcessor
     */
    private $additionalAttributes;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->paymentDO = $this->createMock(PaymentDataObjectInterface::class);
        $this->payment = $this->getMockBuilder(Payment::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->order = $this->getMockBuilder(OrderAdapterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->helper = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->config = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->additionalAttributes =
        $this->paymentprocessor = new PaymentProcessor($this->helper, $this->config);
    }

    /**
     * @return void
     */
    public function testBuildSaleData()
    {
        $invoiceId = 1001;
        $currencyCode = 'USD';
        $amount = "10.00";

        $additionalData = [
            [
                'cc_type',
                self::CC_TYPE
            ],
            [
                'cc_number',
                self::CC_NUMBER
            ],[
                'cc_exp_month',
                self::CC_EXP_MONTH,
            ],[
                'cc_exp_year',
                self::CC_EXP_YEAR
            ],[
                'cc_cid',
                self::CVV
            ],
        ];

        $expectation = [
            'type' => 'auth',
            'amount' => sprintf('%.2F', $amount),
            'ccexp' => sprintf('%02d%02d', self::CC_EXP_MONTH, substr(self::CC_EXP_YEAR, -2)),
            'cvv' => self::CVV,
            'orderid' => $invoiceId,
            'currency' => $currencyCode,
            'ccnumber' => self::CC_NUMBER,
            'customer_vault' => ''
        ];

        $buildSubject = [
            'payment' => $this->paymentDO,
            'amount' => $amount
        ];

        $this->payment->expects(static::exactly(5))
            ->method('getAdditionalInformation')
            ->willReturnMap($additionalData);

        $this->order->expects(static::once())
            ->method('getOrderIncrementId')
            ->willReturn($invoiceId);

        $this->order->expects(static::once())
            ->method('getCurrencyCode')
            ->willReturn($currencyCode);

        $this->paymentDO->expects(static::once())
            ->method('getPayment')
            ->willReturn($this->payment);

        $this->paymentDO->expects(static::once())
            ->method('getOrder')
            ->willReturn($this->order);

        static::assertEquals(
            $expectation,
            $this->paymentprocessor->buildSaleData($buildSubject, 'auth')
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
        $this->paymentprocessor->buildSaleData($buildSubject, 'auth');
    }
}
