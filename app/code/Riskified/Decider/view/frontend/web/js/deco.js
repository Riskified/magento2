define([
    'ko',
    'uiComponent'
], function (ko, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Riskified_Decider/deco'
        },

        /**
         * {@inheritdoc}
         */
        initialize: function () {
            this._super();
        }
    });
});
