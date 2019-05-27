/*
* @since  1.2.0
* @modified 1.8.5
* @author  Deepen
*/
(function ($) {

    // Cached references to DOM elements
    var ina_logout_helpers_dom = {};

    var EDITOR_HEIGHT = "305px";

    var ina_logout_helpers = {

        onReady: function () {
            this.setupDOM();
            this.addEventListeners();
        },
        setupDOM: function () {
            ina_logout_helpers_dom.select2 = $('.ina-hacking-select, .ina-hacking-multi-select');

            //For Warning Section
            ina_logout_helpers_dom.warn_message_enabled = $('.show_on_warn_message_enabled');
            ina_logout_helpers_dom.warn_message_only = $('#ina_show_warn_message_only');

            //ColorPicker and Background
            ina_logout_helpers_dom.colorpicker = $('.ina_color_picker');
            ina_logout_helpers_dom.colorpicker_show = $('.ina_colorpicker_show');
            ina_logout_helpers_dom.background_color = $('.ina_apply_background_color');

            //For Redirect
            ina_logout_helpers_dom.enable_redirect_link = $('#ina_enable_redirect_link');
            ina_logout_helpers_dom.show_on_enable_redirect_link = $('.show_on_enable_redirect_link');
            ina_logout_helpers_dom.hide_message_content = $('.ina_hide_message_content');
            ina_logout_helpers_dom.redirect_page = $('.ina_redirect_page');
            ina_logout_helpers_dom.show_cutom_redirect_textfield = $('.show_cutom_redirect_textfield');

            //Advanced Management Tab
            ina_logout_helpers_dom.resetAdvdata = $('#ina-reset-adv-data');
            ina_logout_helpers_dom.role_timeout = $('#ina_enable_different_role_timeout');
            ina_logout_helpers_dom.multiRoleTable = $('.ina-multi-role-table');

            //Glboals
            ina_logout_helpers_dom.message = $('#message');

            if (ina_logout_helpers_dom.select2.length > 0) {
                ina_logout_helpers_dom.select2.select2();
            }

            if (ina_logout_helpers_dom.colorpicker.length > 0) {
                ina_logout_helpers_dom.colorpicker.wpColorPicker();
            }
        },

        addEventListeners: function () {
            ina_logout_helpers_dom.warn_message_only.on('click', this.showWarnMsgEnabled);
            ina_logout_helpers_dom.background_color.on('click', this.colorpicker);
            ina_logout_helpers_dom.enable_redirect_link.on('click', this.redirect);

            // Reset all Advanced Data
            ina_logout_helpers_dom.resetAdvdata.on('click', this.resetAdvManagementSection);
            ina_logout_helpers_dom.role_timeout.on('click', this.enableDifferentRoleTimeout);

            ina_logout_helpers_dom.redirect_page.on('change', this.showCustomURLInput);
        },

        showWarnMsgEnabled: function () {
            if ($(this).prop("checked")) {
                ina_logout_helpers_dom.warn_message_enabled.find('iframe').css('height', EDITOR_HEIGHT);
                ina_logout_helpers_dom.warn_message_enabled.show();
            } else {
                ina_logout_helpers_dom.warn_message_enabled.hide();
            }
        },

        colorpicker: function () {
            if ($(this).prop("checked")) {
                ina_logout_helpers_dom.colorpicker_show.show();
            } else {
                ina_logout_helpers_dom.colorpicker_show.hide();
            }
        },

        redirect: function () {
            if ($(this).prop("checked")) {
                ina_logout_helpers_dom.show_on_enable_redirect_link.show();
                if (ina_logout_helpers_dom.redirect_page.val() === "custom-page-redirect") {
                    ina_logout_helpers_dom.show_cutom_redirect_textfield.show();
                } else {
                    ina_logout_helpers_dom.show_cutom_redirect_textfield.hide();
                }
            } else {
                ina_logout_helpers_dom.show_on_enable_redirect_link.hide();
                ina_logout_helpers_dom.show_cutom_redirect_textfield.hide();
            }
        },

        resetAdvManagementSection: function (e) {
            e.preventDefault();
            var msg = confirm($(this).data('msg'));
            if (msg) {
                var send_data = {security: ina_other_ajax.ina_security, action: 'ina_reset_adv_settings'};
                $.post(ina_other_ajax.ajaxurl, send_data).done(function (response) {
                    ina_logout_helpers_dom.message.fadeIn().html('<p>' + response.msg + '</p>');
                    setTimeout(function () {
                        location.reload();
                    }, 500);
                });
            } else {
                return false;
            }
        },

        enableDifferentRoleTimeout: function () {
            if ($(this).prop("checked")) {
                ina_logout_helpers_dom.multiRoleTable.show();
            } else {
                ina_logout_helpers_dom.multiRoleTable.hide();
            }
        },

        showCustomURLInput: function (e) {
            if ($(this).val() === "custom-page-redirect") {
                ina_logout_helpers_dom.show_cutom_redirect_textfield.show();
            } else {
                ina_logout_helpers_dom.show_cutom_redirect_textfield.hide();
            }
        }
    };

    $(function () {
        ina_logout_helpers.onReady();
    });

})(jQuery);