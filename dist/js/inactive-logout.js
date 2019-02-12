/**
 * @author Deepen
 * @updated 1.7.2
 * @since 1.0.0
 *
 * This file contains core JS files for the plugin. Do not edit for you will be consumed by the dark side.
 */
var inactive_logout_timeoutID;
var inactive_logout_tabID;
var inactive_logout_timeoutMessage;
var ina_timeout = ina_meta_data.ina_timeout;
var ina_timeout_defined = ina_timeout * 1000; //Minutes
var ina_messageBox = 0;

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
    this.addEventListener("load", ina__heartbeatCustom, false);

    //First get the broswer id
    inactive_logout_tabID = sessionStorage.inactive_logout_tabID && sessionStorage.closedLastTab !== '2' ? sessionStorage.inactive_logout_tabID : sessionStorage.inactive_logout_tabID = Math.random();
    sessionStorage.closedLastTab = '2';
    jQuery(window).on('unload beforeunload', function () {
        sessionStorage.closedLastTab = '1';
    });
    localStorage.setItem("ina__browserTabID", inactive_logout_tabID);

    ina__startTimer();
}

//Call at DOM load
ina__setup_theforce_awakens();

//Starting timeout timer to go into inactive state after 11 seconds if any event like mousemove is not triggered
function ina__startTimer() {
    inactive_logout_timeoutID = window.setTimeout(ina__goInactive, 11000);
}

//Resetting the timer
function ina__resetTimer(e) {
    window.clearTimeout(inactive_logout_timeoutID);
    window.clearTimeout(inactive_logout_timeoutMessage);
    localStorage.setItem("ina__browserTabID", inactive_logout_tabID);
    ina__goActive();
}

/**
 * User is inactive now save last session activity time here
 */
function ina__goInactive() {
    if (ina_messageBox == 0) {
        var dateTime = Date.now();
        var timestamp = Math.floor(dateTime / 1000);

        jQuery(document).ready(function ($) {
            //Update Last Active Status
            var postData = {action: 'ina_checklastSession', do: 'ina_updateLastSession', security: ina_ajax.ina_security, timestamp: timestamp};
            $.post(ina_ajax.ajaxurl, postData).done(function (response) {
                var elem = document.activeElement;
                //IF ACTIVE ELEMENT is clicked inside an iframe then track this following and reset timer again. So, do not logout user from here.
                if (elem && elem.tagName == 'IFRAME') {
                    ina__resetTimer();
                    // console.log("You are browsing inside an IFRAME!");
                } else {
                    // console.log("Last Active on: " + Date.now());
                }

                var browserTabID = localStorage.getItem("ina__browserTabID");
                if (browserTabID == inactive_logout_tabID) {
                    inactive_logout_timeoutMessage = window.setTimeout(ina__showTimeoutMessage, ina_timeout_defined);
                }
            });
        });
    }
}

//Show timeout Message Now
function ina__showTimeoutMessage() {
    var countdown = 10;
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
        };

        var ina_popup_bg_enalbed = $('.ina__no_confict_popup_bg').data('bgenabled');
        if (ina_popup_bg_enalbed) {
            var ina_popup_bg = $('.ina__no_confict_popup_bg').data('bg');
            $('#ina__dp_logout_message_box').css('background', ina_popup_bg);
        }

        ina_messageBox = 1;
        if (ina_warn_message_enabled) {
            //Only show message
            $('#ina__dp_logout_message_box').show();
            $('.ina_stay_logged_in').click(function () {
                document.onkeydown = function (evt) {
                    return true;
                };
                window.oncontextmenu = null;
                $('#ina__dp_logout_message_box').hide();
                ina_messageBox = 0;
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
            var setting_countdown = setInterval(function () {
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
                };
                window.oncontextmenu = null;
                clearTimeout(setting_countdown);
                countdown = 10;
                ina_messageBox = 0;
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

/**
 * Why do i use custom heartbeat when WP already has one ?
 *
 * Some people tend  to reduce the frequency of heartbeat API calls. So, to minimize this issue custom
 * beat API is in place here
 *
 * If any developer visits here - Please sugges the best way :) - Happy to integrate your ideas
 *
 * @since 1.8.0
 */
/*
function ina__heartbeatCustom() {
    $ = jQuery;

    //On load update the time after 2 seconds
    setTimeout(function () {
        $.post(ina_ajax.ajaxurl, {action: 'ina_heartbeat'}).done(function (response) {
        });
    }, 2000);

    //Call ths function every 30 seconds
    setInterval(function () {
        $.post(ina_ajax.ajaxurl, {action: 'ina_heartbeat'}).done(function (response) {
        });
    }, 30000);
}*/
