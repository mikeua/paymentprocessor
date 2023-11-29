/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'paymentprocessor',
                component: 'Mike_PaymentProcessor/js/view/payment/method-renderer/paymentprocessor-method'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);
