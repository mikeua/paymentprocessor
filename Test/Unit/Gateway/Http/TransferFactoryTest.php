<?php

namespace Mike\PaymentProcessor\Test\Unit\Gateway\Http;

use Mike\PaymentProcessor\Gateway\Config\Config;
use Mike\PaymentProcessor\Gateway\Http\TransferFactory;
use Magento\Payment\Gateway\Http\TransferBuilder;
use Magento\Payment\Gateway\Http\TransferInterface;
use PHPUnit\Framework\MockObject\MockObject;

class TransferFactoryTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @var TransferFactory
     */
    private $transferFactory;

    /**
     * @var string
     */
    private $transferMock;

    /**
     * @var Config
     */
    private $config;
    /**
     * @var TransferBuilder|MockObject
     */
    private $transferBuilder;

    /**
     * setUp function
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->transferBuilder = $this->createMock(TransferBuilder::class);
        $this->config = $this->getMockBuilder(Config::class)
        ->disableOriginalConstructor()
        ->getMock();

        $this->transferMock = $this->createMock(TransferInterface::class);

        $this->transferFactory = new TransferFactory(
            $this->transferBuilder,
            $this->config
        );
    }

    /**
     * testCreate function
     *
     * @return void
     */
    public function testCreate()
    {
        $request = [
            'parameter' => 'value'
        ];
        $this->transferBuilder->expects($this->once())
            ->method('setBody')
            ->with($request)
            ->willReturnSelf();
        $this->transferBuilder->expects($this::once())
            ->method('setMethod')
            ->with('POST')
            ->willReturnSelf();
        $this->transferBuilder->expects($this::once())
            ->method('shouldEncode')
            ->with(true)
            ->willReturnSelf();
        $this->transferBuilder->expects($this::once())
            ->method('setUri')
            ->with($this->config->getGatewayUrl())
            ->willReturnSelf();
        $this->transferBuilder->expects($this::once())
            ->method('setClientConfig')
            ->with(['timeout' => 60])
            ->willReturnSelf();
        $this->transferBuilder->expects($this::once())
            ->method('build')
            ->willReturn($this->transferMock);
        $this->assertEquals($this->transferMock, $this->transferFactory->create($request));
    }
}
