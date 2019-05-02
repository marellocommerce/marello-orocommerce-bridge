/*jslint nomen:true*/
/*global define*/
define(function(require) {
    'use strict';

    var $ = require('jquery');

    var OroCommerceConnectionValidatorComponent;
    var BaseComponent = require('oroui/js/app/components/base/component');
    var LoadingMaskView = require('oroui/js/app/views/loading-mask-view');
    var messenger = require('oroui/js/messenger');

    OroCommerceConnectionValidatorComponent = BaseComponent.extend({

        /**
         * @property {jquery} $button
         */
        $button: null,

        /**
         * @property {jquery} $form
         */
        $form: null,

        /**
         * @property {string} backendUrl
         */
        backendUrl: '',

        /**
         * @property {LoadingMaskView} loadingMaskView
         */
        loadingMaskView: null,

        /**
         * @inheritDoc
         */
        initialize: function(options) {
            this.$button = options._sourceElement;
            this.$form = $(options.formSelector);
            this.backendUrl = options.backendUrl;
            this.loadingMaskView = new LoadingMaskView({container: $('body')});

            this.initListeners();
        },

        initListeners: function() {
            this.$button.on('click', this.buttonClickHandler.bind(this));
        },

        buttonClickHandler: function() {
            this.$form.validate();

            if (this.$form.valid()) {
                this.checkOroCommerceConnection();
            }
        },

        checkOroCommerceConnection: function() {
            var self = this;

            $.ajax({
                url: this.backendUrl,
                type: 'POST',
                data: this.$form.serialize(),
                beforeSend: function() {
                    self.loadingMaskView.show();
                },
                success: this.successHandler.bind(this),
                complete: function() {
                    self.loadingMaskView.hide();
                }
            });
        },

        /**
         * @param {{success: bool, message: string}} response
         */
        successHandler: function(response) {
            var type = 'error';
            if (response.success) {
                type = 'success';
            }

            messenger.notificationFlashMessage(type, response.message);
        },

        dispose: function() {
            if (this.disposed) {
                return;
            }

            this.$button.off('click');

            OroCommerceConnectionValidatorComponent.__super__.dispose.call(this);
        }
    });

    return OroCommerceConnectionValidatorComponent;
});
