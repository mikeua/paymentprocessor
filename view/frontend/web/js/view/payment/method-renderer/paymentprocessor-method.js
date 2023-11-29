/*browser:true*/
/*global define*/
define(
    [
        'Magento_Payment/js/view/payment/cc-form',
        'jquery',
        'Magento_Payment/js/model/credit-card-validation/validator',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Checkout/js/action/place-order',
        'Magento_Ui/js/model/messageList',
        'Magento_Vault/js/view/payment/vault-enabler',
        'ko'
    ],
    function (Component, $, v, fullScreenLoader,placeOrderAction,messageList,VaultEnabler, ko) {
        'use strict';
        return Component.extend({
            cardNumberIsValid: ko.observable(false),
            cvvIsValid:  ko.observable(false),
            /**
             * @returns {exports.initialize}
             */
            initialize: function () {

                this._super();
                this.vaultEnabler = new VaultEnabler();
                this.vaultEnabler.setPaymentCode(this.getVaultCode());
                return this;
            },
            defaults: {
                template: 'Mike_PaymentProcessor/payment/paymentprocessor-form'
            },
            /** Returns send check to info */
            getMailingAddress: function () {
                return window.checkoutConfig.payment.checkmo.mailingAddress;
            },
            getCode: function () {
                return 'paymentprocessor';
            },
            /**
             * Get data
             *
             * @returns {Object}
             */
            getData: function () {
                var data = {
                    'method': this.getCode(),
                    'additional_data': {
                        'cc_type': this.selectedCardType(),
                        'cc_number': $('#paymentprocessor_cc_number').val(),
                        'cc_exp_month': $('#paymentprocessor_expiration').val(),
                        'cc_exp_year': $('#paymentprocessor_expiration_yr').val(),
                        'cc_cid': $('#paymentprocessor_cc_cid').val()
                    }
                };
                data['additional_data'] = _.extend(data['additional_data'], this.additionalData);
                this.vaultEnabler.visitAdditionalData(data);
                return data;
            },
            isActive: function () {
                return true;
            },
            validate: function () {
                var self = this;
                var $form = $('#' + self.getCode() + '-form');
                return $form.validation() && $form.validation('isValid');
            },
            /**
             * Prepare data to place order
             * @param {Object} data
             */
            PlaceOrder: function (data, event) {
                debugger;
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
            /**
             * Show error message
             * @param {String} errorMessage
             */
            showError: function (errorMessage) {
                let statusElement = document.getElementById('transaction-status');
                statusElement.innerHTML = errorMessage;
                statusElement.style.color = "red";
                statusElement.focus();
            },
            /**
             * @returns {Bool}
             */
            isVaultEnabled: function () {
                return this.vaultEnabler.isVaultEnabled();
            },
            /**
             * @returns {String}
             */
            getVaultCode: function () {
                return window.checkoutConfig.payment[this.getCode()].ccVaultCode;
            }
        });
    }
);
