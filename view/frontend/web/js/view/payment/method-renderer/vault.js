define([
    'Magento_Vault/js/view/payment/method-renderer/vault'
], function (VaultComponent) {
    'use strict';

    return VaultComponent.extend({
        defaults: {
            template: 'Magento_Vault/payment/form'
        },
        /**
         * Get last 4 digits of card
         * @returns {String}
         */
        getMaskedCard: function () {
            return this.details.maskedCC;
        },

        /**
         * Get expiration date
         * @returns {String}
         */
        getExpirationDate: function () {
            return this.details.expirationDate;
        },

        /**
         * Get card type
         * @returns {String}
         */
        getCardType: function () {
            return this.details.type;
        },

        /**
         * Get public hash
         * @returns {String}
         */
        getToken: function () {
            return this.publicHash;
        },
        /**
         * Prepare data to place order
         * @param {Object} data
         */
        PlaceOrder: function (data, event) {
            if (event) {
                event.preventDefault();
            }
            var self = this;
            if (self.validate()) {
                self.isPlaceOrderActionAllowed(false);
                if (!self.cardNumberIsValid() || !self.cvvIsValid()) {
                    self.showError(!this.cardNumberIsValid() ? "Invalid card" : "Invalid CVV");
                    self.isPlaceOrderActionAllowed(true);
                    return false;
                }
                return false
            } else {
                return false
            }
        },
    });
});


