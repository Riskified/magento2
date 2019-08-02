define([
    'jquery',
    'mage/utils/wrapper',
    'mage/storage',
    'Magento_Braintree/js/view/payment/3d-secure',
    'Magento_Checkout/js/model/quote'
], function ($, wrapper, storage, verify3DSecure, quote) {
    'use strict';

    console.log('step-1');
    return function (braintreeValidatorHandler) {
        braintreeValidatorHandler.validate = function(context, callback) {
            var self = this,
                config = this.getConfig(),
                deferred;

            // no available validators
            if (!self.validators.length) {

                let serviceUrl = window.location.origin + "/decider/advice/call",
                    payload = { id: quote.getQuoteId(), location: "Boston" },
                    adviceStatus = false;

                $.ajax({
                    method: "POST",
                    async: false,
                    url: serviceUrl,
                    data: payload
                }).done(function( status ){
                    adviceStatus = status;
                    });

                callback();

                if(config[verify3DSecure.getCode()].enabled){
                    verify3DSecure.setConfig(config[verify3DSecure.getCode()]);
                    self.add(verify3DSecure);
                }

                return;
            }

            // get list of deferred validators
            deferred = $.map(self.validators, function (current) {
                return current.validate(context);
            });

            $.when.apply($, deferred)
                .done(function () {
                    callback();
                }).fail(function (error) {
                self.showError(error);
            });
        };
        braintreeValidatorHandler.initRiskifiedAdviceCall = function(serviceUrl, payload) {
            return storage.post(
                serviceUrl, payload
            ).fail(
                function (response) {
                    errorProcessor.process(response, messageContainer);
                }
            ).always(
                function () {
                    fullScreenLoader.stopLoader();
                }
            );
        };

        return braintreeValidatorHandler;
    };
});