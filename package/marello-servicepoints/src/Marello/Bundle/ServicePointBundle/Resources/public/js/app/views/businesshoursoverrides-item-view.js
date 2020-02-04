define(function(require) {
    'use strict';

    var BusinessHoursOverridesItemView,
        $ = require('jquery'),
        mediator = require('oroui/js/mediator'),
        AbstractItemView = require('marellolayout/js/app/views/abstract-item-view');

    /**
     * @export marelloservicepoint/js/app/views/businesshoursoverrides-item-view
     * @extends marellolayout.app.views.AbstractItemView
     * @class marelloservicepoint.app.views.BusinessHoursOverridesItemView
     */
    BusinessHoursOverridesItemView = AbstractItemView.extend({
        options: {
            ftid: "",
            openStatusSelector: ""
        },

        $openStatusSelect: null,

        /**
         * @property {Object}
         */
        data: {},

        /**
         * @inheritDoc
         */
        initialize: function(options) {
            this.options = $.extend(true, {}, this.options, options || {});
            console.log(this.options.openStatusSelector);
            this.$openStatusSelect = this.$el.find(this.options.openStatusSelector);

            BusinessHoursOverridesItemView.__super__.initialize.apply(this, arguments);

            this.bindEvents();
            this.initTimePeriods();
        },

        bindEvents: function() {
            this.$openStatusSelect.on('change', this.onOpenStatusChange);
        },

        initTimePeriods: function() {
            this.$openStatusSelect.trigger('change');
        },

        onOpenStatusChange: function(event) {
            var $target = $(event.target);
            var value = $target.val();
            var selector = $target.closest('.marello-line-item').data('content');

            mediator.trigger('servicepointfacility-businesshours:openstatus:change', {
                value: value,
                businessHoursRowSelector: '[data-content="' + selector + '"]',
            });
        },

        dispose: function() {
            if (this.disposed) {
                return;
            }

            if (this.$openStatusselect) {
                this.$openStatusSelect.off();
            }

            this.$el.off();

            BusinessHoursOverridesItemView.__super__.dispose.apply(this, arguments);
        }
    });

    return BusinessHoursOverridesItemView;
});
