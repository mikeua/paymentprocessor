<?php

namespace Mike\PaymentProcessor\Test\Unit\Model\Adminhtml\Source;

use Mike\PaymentProcessor\Model\Adminhtml\Source\PaymentAction;

class PaymentActionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return void
     */
    public function testToOptionArray()
    {
        $sourceModel = new PaymentAction();

        static::assertEquals(
            [
                [
                    'value' => 'authorize',
                    'label' => __('Authorize'),
                ],
                [
                    'value' => 'authorize_capture',
                    'label' => __('Authorize and Capture'),
                ],
            ],
            $sourceModel->toOptionArray()
        );
    }
}
