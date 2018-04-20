/**
 * @author Deepen
 * @updated 1.7.2
 * @since 1.0.0
 *
 * This file contains core JS files for the plugin. Do not edit for you will be consumed by the dark side.
 */
var timeoutID;
var tabID;
var timeoutMessage;
var ina_timeout = ina_meta_data.ina_timeout;
var ina_warn_time = ina_meta_data.ina_warn_time;
var timeout_defined = ina_timeout * 1000; //Minutes
var warn_time_defined = ina_warn_time * 1000; //seconds
var messageBox = 0;

/**
 * Initial Plugin JS load
 */
function ina__setup_theforce_awakens() {
    this.addEventListener("mousemove", ina__resetTimer, false);
    this.addEventListener("mousedown", ina__resetTimer, false);
    this.addEventListener("keypress", ina__resetTimer, false);
    this.addEventListener("DOMMouseScroll", ina__resetTimer, false);
    this.addEventListener("mousewheel", ina__resetTimer, false);
    this.addEventListener("touchmove", ina__resetTimer, false);
    this.addEventListener("MSPointerMove", ina__resetTimer, false);

    //First get the broswer id
    tabID = sessionStorage.tabID && sessionStorage.closedLastTab !== '2' ? sessionStorage.tabID : sessionStorage.tabID = Math.random();
    sessionStorage.closedLastTab = '2';
    jQuery(window).on('unload beforeunload', function () {
        sessionStorage.closedLastTab = '1';
    });
    localStorage.setItem("ina__browserTabID", tabID);

    ina__startTimer();
}

//Call at DOM load
ina__setup_theforce_awakens();

//Starting timeout timer to go into inactive state after 15 seconds if any event like mousemove is not triggered
function ina__startTimer() {
    timeoutID = window.setTimeout(ina__goInactive, 15000);
}

//Resetting the timer
function ina__resetTimer(e) {
    window.clearTimeout(timeoutID);
    window.clearTimeout(timeoutMessage);
    localStorage.setItem("ina__browserTabID", tabID);
    ina__goActive();
}

/**
 * User is inactive now save last session activity time here
 */
function ina__goInactive() {
    if (messageBox == 0) {
        var dateTime = Date.now();
        var timestamp = Math.floor(dateTime / 1000);

        jQuery(document).ready(function ($) {
            //Update Last Active Status
            var postData = {action: 'ina_checklastSession', do: 'ina_updateLastSession', security: ina_ajax.ina_security, timestamp: timestamp};
            $.post(ina_ajax.ajaxurl, postData).done(function (response) {
                console.log("Last Active on: " + Date.now());
                var browserTabID = localStorage.getItem("ina__browserTabID");
                if (browserTabID == tabID) {
                    timeoutMessage = window.setTimeout(ina__showTimeoutMessage, (timeout_defined - warn_time_defined));
                }
            });
        });
    }
}

//Show timeout Message Now
function ina__showTimeoutMessage() {
    var countdown = ina_warn_time;
    var t;
    var ina_disable_countdown = ina_meta_data.ina_disable_countdown;
    var ina_warn_message_enabled = ina_meta_data.ina_warn_message_enabled;
    jQuery(function ($) {
        document.onkeydown = function (evt) {
            var keycode = evt.charCode || evt.keyCode;
            //Disable all keys except F5
            if (keycode != 116) return false;
        }

        //Disable Right Click
        window.oncontextmenu = function () {
            return false;
        }

        var ina_popup_bg_enalbed = $('.ina__no_confict_popup_bg').data('bgenabled');
        if (ina_popup_bg_enalbed) {
            var ina_popup_bg = $('.ina__no_confict_popup_bg').data('bg');
            $('#ina__dp_logout_message_box').css('background', ina_popup_bg);
        }

        messageBox = 1;
        if (ina_warn_message_enabled) {
            //Only show message
            $('#ina__dp_logout_message_box').show();
            $('.ina_stay_logged_in').click(function () {
                document.onkeydown = function (evt) {
                    return true;
                }
                window.oncontextmenu = null;
                $('#ina__dp_logout_message_box').hide();
                messageBox = 0;
            });
        } else if (ina_disable_countdown) {
            //Disabled Countdown but directly logout
            var postData = {action: 'ina_checklastSession', do: 'ina_logout', security: ina_ajax.ina_security};
            $.post(ina_ajax.ajaxurl, postData).done(function (op) {
                if (op.redirect_url) {
                    $('#ina__dp_logout_message_box').show();
                    $('#ina__dp_logout_message_box .ina-dp-noflict-modal-body').html('<p>' + op.msg + '<p>');

                    //Logout Now
                    ina__logout_now(op.redirect_url);
                } else {
                    $('#ina__dp_logout_message_box').show();
                    $('#ina__dp_logout_message_box .ina-dp-noflict-modal-body').html('<p>' + op.msg + '<p><p class="ina-dp-noflict-btn-container"><a class="btn-timeout" href="javascript:void(0);" onclick="window.location.reload();">OK</a></p>');

                    //Logout Now
                    ina__logout_now(false);
                }
                return false;
            });
        } else {
            $('#ina__dp_logout_message_box').show();
            setting_countdown = setInterval(function () {
                if (countdown >= 0) {
                    t = countdown--;
                    $(".ina_countdown").html('(' + t + ')');
                }

                if (t == 0) {
                    clearTimeout(setting_countdown);
                    var postData = {action: 'ina_checklastSession', do: 'ina_logout', security: ina_ajax.ina_security};
                    $.post(ina_ajax.ajaxurl, postData).done(function (op) {
                        if (op.redirect_url) {
                            $('#ina__dp_logout_message_box .ina-dp-noflict-modal-body').html('<p>' + op.msg + '<p>');

                            //Logout Now
                            ina__logout_now(op.redirect_url);
                        } else {
                            $('#ina__dp_logout_message_box .ina-dp-noflict-modal-body').html('<p>' + op.msg + '<p><p class="ina-dp-noflict-btn-container"><a class="btn-timeout" href="javascript:void(0);" onclick="window.location.reload();">OK</a></p>');

                            //Logout Now
                            ina__logout_now(false);
                        }
                        return false;
                    });
                }
            }, 1000);

            $('.ina_stay_logged_in').click(function () {
                document.onkeydown = function (evt) {
                    return true;
                }
                window.oncontextmenu = null;
                clearTimeout(setting_countdown);
                countdown = 10;
                messageBox = 0;
                $('#ina__dp_logout_message_box').hide();
                $('.ina_countdown').text('');
            });
        }
    });
}

/**
 * User is actively Working and Browsing
 */
function ina__goActive() {
    ina__startTimer();
}

/**
 * Logout User Here
 * @param redirect_url
 */
function ina__logout_now(redirect_url) {
    $ = jQuery;
    var logoutData = {action: 'ina_logout_session', security: ina_ajax.ina_security};
    $.post(ina_ajax.ajaxurl, logoutData).done(function () {
        if (redirect_url) {
            window.location = redirect_url;
        }

        //Logged Out
        console.log("Session Logged Out !!");
    });
}