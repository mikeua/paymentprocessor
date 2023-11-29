<?php
namespace Mike\PaymentProcessor\Gateway\Request;

use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;

class DataRequest implements BuilderInterface
{
    /**
     * Builds ENV request
     *
     * @param array<string,mixed>|array<mixed> $buildSubject
     * @return array<string,mixed>|array<mixed>
     */
    public function build(array $buildSubject): array
    {
        if (!isset($buildSubject['payment'])
            || !$buildSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = $buildSubject['payment'];
        $payment = $paymentDO->getPayment();

        $order = $paymentDO->getOrder();
        /** @var \Magento\Payment\Gateway\Data\AddressAdapterInterface $billing */
        $billing = $order->getBillingAddress();
        /** @var \Magento\Payment\Gateway\Data\AddressAdapterInterface|null $shipping */
        $shipping = $order->getShippingAddress();
        // for virtual products
        if (!$shipping) {
            $shipping = $billing;
        }
        //regular capture
        if ($payment->getLastTransId()) {
            return [];
        }
        //authorize and capture
        return [
            'firstname' => $billing->getFirstname(),
            'lastname' => $billing->getLastname(),
            'company' => $billing->getCompany(),
            'address1' => $billing->getStreetLine1(),
            'address2' => $billing->getStreetLine2(),
            'city' => $billing->getCity(),
            'state' => $billing->getRegionCode(),
            'zip' => $billing->getPostcode(),
            'country'=> $billing->getCountryId(),
            'phone' => $billing->getTelephone(),
            'shipping_firstname' => $shipping->getFirstname(),
            'shipping_lastname' => $shipping->getLastname(),
            'shipping_company' => $shipping->getCompany(),
            'shipping_address1' => $shipping->getStreetLine1(),
            'shipping_address2'=> $shipping->getStreetLine2(),
            'shipping_city' => $shipping->getCity(),
            'shipping_state' => $shipping->getRegionCode(),
            'shipping_zip' => $shipping->getPostcode(),
            'shipping_country' => $shipping->getCountryId(),
            'shipping_email' => $billing->getEmail(),
        ];
    }
}
