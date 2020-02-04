define(function(require) {
    'use strict';

    var BusinessHoursOverridesView,
        $ = require('jquery'),
        mediator = require('oroui/js/mediator'),
        AbstractItemsView = require('marellolayout/js/app/views/abstract-items-view');

    /**
     * @export marelloservicepoint/js/app/views/businesshoursoverrides-items-view
     * @extends marellolayout.app.views.AbstractItemsView
     * @class marelloservicepoint.app.views.BusinessHoursOverridesView
     */
    BusinessHoursOverridesView = AbstractItemsView.extend({
        /**
         * @property {Object}
         */
        options: {
            closedValue: "closed"
        },

        /**
         * @inheritDoc
         */
        initialize: function(options) {
            this.options = $.extend(true, {}, this.options, options || {});
            BusinessHoursOverridesView.__super__.initialize.apply(this, arguments);

            this.bindEvents();
        },

        bindEvents: function() {
            mediator.subscribe('servicepointfacility-businesshours:openstatus:change', this.toggleTimePeriods, this);
        },

        toggleTimePeriods: function(e) {
            if (!e.businessHoursRowSelector) {
                return;
            }

            if (e.value === this.options.closedValue) {
                this.hideTimePeriods(e.businessHoursRowSelector);
            } else {
                this.showTimePeriods(e.businessHoursRowSelector);
            }
        },

        hideTimePeriods: function(selector) {
            var $container = $(selector).find('.businesshoursoverrides-line-item-timeperiods');
            $container.children().each(function () {
                $(this).hide();
            });
        },

        showTimePeriods: function(selector) {
            var $container = $(selector).find('.businesshoursoverrides-line-item-timeperiods');
            $container.show();
            $container.children().each(function () {
                $(this).show();
            });
        },
    });

    return BusinessHoursOverridesView;
});
