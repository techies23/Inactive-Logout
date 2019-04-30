/*
* @since  1.2.0
* @author  Deepen
*/
jQuery(function ($) {
<<<<<<< HEAD:dist/js/inactive-logout-other.js
    if( $('.ina-hacking-select').length >0 ) {
        $('.ina-hacking-select').select2();
    }

    if( $(".ina-hacking-multi-select").length >0 ) {
        $(".ina-hacking-multi-select").select2({width: '500px', placeholder: "Select Roles"});
    }
=======
    $('.ina-hacking-select').select2();
    $(".ina-hacking-multi-select").select2({width: '500px', placeholder: "Select Roles"});
    const EDITOR_HEIGHT = "305px";
>>>>>>> d3b292c11e2301308178611f0504ebab8233f981:assets/js/inactive-logout-other.js

    //FOR SHOW WARN BOX CHECKBOX
    $('#ina_show_warn_message_only').click(function () {
        if ($(this).prop("checked")) {
	        $('.show_on_warn_message_enabled iframe').css('height', EDITOR_HEIGHT);
            $('.show_on_warn_message_enabled').show();
        } else {
            $('.show_on_warn_message_enabled').hide();
        }
    });

    // Add Color Picker to all inputs that have 'color-field' class
    $('.ina_color_picker').wpColorPicker();

    $('input[name="ina_full_overlay"]').click(function () {
        if ($(this).prop("checked")) {
            $('.ina_colorpicker_show').show();
        } else {
            $('.ina_colorpicker_show').hide();
        }
    });

    //FOR REDIRECT CHECKBOX
    $('#ina_enable_redirect_link').click(function () {
        if ($(this).prop("checked")) {
            $('.show_on_enable_redirect_link').show();
            $('.ina_hide_message_content').hide();

            if ($('select[name=ina_redirect_page]').val() == "custom-page-redirect") {
                $('.show_cutom_redirect_textfield').show();
            } else {
                $('.show_cutom_redirect_textfield').hide();
            }
        } else {
            $('.show_on_enable_redirect_link').hide();
            $('.ina_hide_message_content iframe').css('height', EDITOR_HEIGHT);
            $('.ina_hide_message_content').show();
            $('.show_cutom_redirect_textfield').hide();
        }
    });

    //FOR ADV SETTINGS MULTI ROLE ENABLE CHECKBOX
    $('#ina_enable_different_role_timeout').click(function () {
        if ($(this).prop("checked")) {
            $('.ina-multi-role-table, .hide-description-ina').show();
        } else {
            $('.ina-multi-role-table, .hide-description-ina').hide();
        }
    });

    //Select the custom page redirect then show a custom url text field
    $('select[name=ina_redirect_page]').change(function () {
        if ($(this).val() == "custom-page-redirect") {
            $('.show_cutom_redirect_textfield').show();
        } else {
            $('.show_cutom_redirect_textfield').hide();
        }
    });

    /**
     * Reset all Advanced Data
     * @since  1.3.0
     * @author  Deepen
     */
    $('#ina-reset-adv-data').click(function () {
        var msg = confirm($(this).data('msg'));
        if (msg) {
            var send_data = {security: ina_other_ajax.ina_security, action: 'ina_reset_adv_settings'};
            $('#ina-cover-loading').show();
            $.post(ina_other_ajax.ajaxurl, send_data).done(function (response) {
                $('#ina-cover-loading').fadeOut("slow");
                $('#message').fadeIn().html('<p>' + response.msg + '</p>');
                setTimeout(function () {
                    location.reload();
                }, 500);
            });
        } else {
            return false;
        }
    });
});
