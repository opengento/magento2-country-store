/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */

define([
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'underscore'
], function (Component, customerData, _) {
    'use strict';

    return Component.extend({
        initialize: function () {
            this._super();
            this.countryStoreData = customerData.get('country_store_data');

            if (this.isInvalidated()) {
                customerData.set('country_store_data', {'reload': true});
                customerData.reload(['country_store_data']);
            }
        },

        isInvalidated: function () {
            return !this.countryStoreData().code &&
                !this.countryStoreData().reload &&
                !_.contains(customerData.getExpiredSectionNames(), 'country_store_data');
        }
    });
});
