<?php

namespace Mike\PaymentProcessor\Model\Adminhtml\Source;

class PaymentAction implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @return array<string, mixed>|array<mixed>
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => 'authorize',
                'label' => __('Authorize'),
            ],
            [
                'value' => 'authorize_capture',
                'label' => __('Authorize and Capture'),
            ],
        ];
    }
}
