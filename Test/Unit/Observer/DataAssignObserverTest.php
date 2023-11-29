<?php

namespace Mike\PaymentProcessor\Test\Unit\Observer;

use Mike\PaymentProcessor\Observer\DataAssignObserver;
use Magento\Framework\DataObject;
use Magento\Framework\Event;
use Magento\Payment\Model\InfoInterface;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;
use PHPUnit\Framework\MockObject\MockObject;

class DataAssignObserverTest extends \PHPUnit\Framework\TestCase
{

    public const CC_TYPE = 'VI';
    public const CC_NUMBER = '4444333322221111';
    public const CVV = '123';
    public const CC_EXP_MONTH = 10;
    public const CC_EXP_YEAR = 2028;
    /**
     * @var (Event\Observer&MockObject)|MockObject
     */
    private $observerContainer;
    /**
     * @var (Event&MockObject)|MockObject
     */
    private $event;
    /**
     * @var InfoInterface|MockObject
     */
    private $paymentInfoModel;
    /**
     * @var DataObject
     */
    private $dataObject;
    /**
     * @var DataAssignObserver
     */
    private $observer;
    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->observerContainer = $this->getMockBuilder(Event\Observer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->event = $this->getMockBuilder(Event::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->paymentInfoModel = $this->createMock(InfoInterface::class);
        $this->dataObject = new DataObject(
            [
                PaymentInterface::KEY_ADDITIONAL_DATA => [
                    'cc_type' => self::CC_TYPE,
                    'cc_number' => self::CC_NUMBER,
                    'cc_exp_month' => self::CC_EXP_MONTH,
                    'cc_exp_year' => self::CC_EXP_YEAR,
                    'cc_cid' => self::CVV
                ]

            ]
        );
        $this->observer = new DataAssignObserver();
    }

    /**
     * @return void
     */
    public function testExecute()
    {
        $this->observerContainer->expects(static::atLeastOnce())
            ->method('getEvent')
            ->willReturn($this->event);
        $this->event->expects(static::exactly(2))
            ->method('getDataByKey')
            ->willReturnMap(
                [
                    [AbstractDataAssignObserver::MODEL_CODE, $this->paymentInfoModel],
                    [AbstractDataAssignObserver::DATA_CODE, $this->dataObject]
                ]
            );

        $this->paymentInfoModel->expects(static::at(0))
            ->method('setAdditionalInformation')
            ->with(
                'cc_type',
                self::CC_TYPE
            );
        $this->paymentInfoModel->expects(static::at(1))
            ->method('setAdditionalInformation')
            ->with(
                'cc_number',
                self::CC_NUMBER
            );

        $this->paymentInfoModel->expects(static::at(2))
            ->method('setAdditionalInformation')
            ->with(
                'cc_exp_month',
                self::CC_EXP_MONTH
            );

        $this->paymentInfoModel->expects(static::at(3))
            ->method('setAdditionalInformation')
            ->with(
                'cc_exp_year',
                self::CC_EXP_YEAR
            );

        $this->paymentInfoModel->expects(static::at(4))
            ->method('setAdditionalInformation')
            ->with(
                'cc_cid',
                self::CVV
            );

        $this->observer->execute($this->observerContainer);
    }
}
