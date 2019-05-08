/**
 * @author Deepen
 * @updated 1.7.2
 * @since 1.0.0
 *
 * This file contains core JS files for the plugin. Do not edit for you will be consumed by the dark side.
 */
(function ($) {
    var inactive_logout_timeoutID;
    var inactive_logout_tabID;
    var inactive_logout_timeoutMessage;
    var ina_timeout = ina_meta_data.ina_timeout;
    var ina_timeout_defined = ina_timeout * 1000; //Minutes
    // var ina_timeout_defined = 1000; //Minutes
    var ina_messageBox = 0;
    var ina_dom = {};
    var countdown = 10;
    var setting_countdown;

    var inactiveLogout = {

        onReady: function () {
            this.setupDOM();
            this.eventListeners();
        },

        setupDOM: function () {
            ina_dom.stayLoggedIn = $('.ina_stay_logged_in');

            //Background
            ina_dom.popupBG = $('.ina__no_confict_popup_bg');

            //Globals
            ina_dom.msg_box = $('#ina__dp_logout_message_box');
            ina_dom.msg_boxBody = $('#ina__dp_logout_message_box .ina-dp-noflict-modal-body');
            ina_dom.countdown = $(".ina_countdown");
        },

        eventListeners: function () {
            $(document).on("mousemove", this.resetTimer);
            $(document).on("mousedown", this.resetTimer);
            $(document).on("keypress", this.resetTimer);
            $(document).on("DOMMouseScroll", this.resetTimer);
            $(document).on("mousewheel", this.resetTimer);
            $(document).on("touchmove", this.resetTimer);
            $(document).on("MSPointerMove", this.resetTimer);
            $(window).on("load", this.resetTimer);

            ina_dom.stayLoggedIn.on("click", this.stayLoggedInWarnMsg);
        },

        //Resetting the timer
        resetTimer: function () {
            window.clearTimeout(inactive_logout_timeoutID);
            window.clearTimeout(inactive_logout_timeoutMessage);
            localStorage.setItem("ina__browserTabID", inactive_logout_tabID);

            try {
                inactiveLogout.goActive();
            } catch (e) {
                if (e instanceof TypeError) {
                    console.log(e, true);
                } else {
                    console.log(e, true);
                }
            }

        },

        //Starting timeout timer to go into inactive state after 11 seconds if any event like mousemove is not triggered
        startTimer: function () {
            inactive_logout_timeoutID = window.setTimeout(this.goInactive, 11000);
            // inactive_logout_timeoutID = window.setTimeout(this.goInactive, 1000);
        },

        goActive: function () {
            try {
                this.startTimer();
            } catch (e) {
                if (e instanceof TypeError) {
                    console.log(e, true);
                } else {
                    console.log(e, true);
                }
            }
        },

        /**
         * User is inactive now save last session activity time here
         */
        goInactive: function () {
            if (ina_messageBox === 0) {
                var dateTime = Date.now();
                var timestamp = Math.floor(dateTime / 1000);

                //Update Last Active Status
                var postData = {action: 'ina_checklastSession', do: 'ina_updateLastSession', security: ina_ajax.ina_security, timestamp: timestamp};
                $.post(ina_ajax.ajaxurl, postData).done(function (response) {
                    var elem = document.activeElement;
                    if (elem && elem.tagName === 'IFRAME') {
                        this.resetTimer();
                    }

                    window.setTimeout(inactiveLogout.showTimeoutMessage, ina_timeout_defined);
                });
            }
        },

        //Show timeout Message Now
        showTimeoutMessage: function () {
            var t;
            var ina_disable_countdown = ina_meta_data.ina_disable_countdown;
            var ina_warn_message_enabled = ina_meta_data.ina_warn_message_enabled;

            document.onkeydown = function (evt) {
                var keycode = evt.charCode || evt.keyCode;
                //Disable all keys except F5
                if (keycode != 116) return false;
            };

            //Disable Right Click
            window.oncontextmenu = function () {
                return false;
            };

            var ina_popup_bg_enalbed = ina_dom.popupBG.data('bgenabled');
            if (ina_popup_bg_enalbed) {
                var ina_popup_bg = ina_dom.popupBG.data('bg');
                ina_dom.msg_box.css('background', ina_popup_bg);
            }

            ina_messageBox = 1;
            if (ina_warn_message_enabled) {
                //Only show message
                ina_dom.msg_box.show();
            } else if (ina_disable_countdown) {
                //Disabled Countdown but directly logout
                var postData = {action: 'ina_checklastSession', do: 'ina_logout', security: ina_ajax.ina_security};
                $.post(ina_ajax.ajaxurl, postData).done(function (op) {
                    if (op.redirect_url) {
                        ina_dom.msg_box.show();
                        ina_dom.msg_boxBody.html('<p>' + op.msg + '<p>');

                        //Logout Now
                        inactiveLogout.logout_now(op.redirect_url);
                    } else {
                        ina_dom.msg_box.show();
                        ina_dom.msg_boxBody.html('<p>' + op.msg + '<p><p class="ina-dp-noflict-btn-container"><a class="btn-timeout" href="javascript:void(0);" onclick="window.location.reload();">OK</a></p>');

                        //Logout Now
                        inactiveLogout.logout_now(false);
                    }
                    return false;
                });
            } else {
                ina_dom.msg_box.show();
                setting_countdown = setInterval(function () {
                    if (countdown >= 0) {
                        t = countdown--;
                        ina_dom.countdown.html('(' + t + ')');
                    }

                    if (t === 0) {
                        clearTimeout(setting_countdown);
                        var postData = {action: 'ina_checklastSession', do: 'ina_logout', security: ina_ajax.ina_security};
                        $.post(ina_ajax.ajaxurl, postData).done(function (op) {
                            if (op.redirect_url) {
                                ina_dom.msg_boxBody.html('<p>' + op.msg + '<p>');

                                //Logout Now
                                inactiveLogout.logout_now(op.redirect_url);
                            } else {
                                ina_dom.msg_boxBody.html('<p>' + op.msg + '<p><p class="ina-dp-noflict-btn-container"><a class="btn-timeout" href="javascript:void(0);" onclick="window.location.reload();">OK</a></p>');

                                //Logout Now
                                inactiveLogout.logout_now(false);
                            }
                            return false;
                        });
                    }
                }, 1000);
            }
        },

        stayLoggedInWarnMsg: function () {
            document.onkeydown = function (evt) {
                return true;
            };

            window.oncontextmenu = null;
            clearTimeout(setting_countdown);
            countdown = 10;
            ina_messageBox = 0;
            ina_dom.msg_box.hide();
            ina_dom.countdown.text('');
        },

        /**
         * Logout User Here
         * @param redirect_url
         */
        logout_now: function (redirect_url) {
            var logoutData = {action: 'ina_logout_session', security: ina_ajax.ina_security};
            $.post(ina_ajax.ajaxurl, logoutData).done(function () {
                if (redirect_url) {
                    window.location = redirect_url;
                }

                //Logged Out
                console.log("Session Logged Out !!");
            });
        }
    };

    $(function () {
        inactiveLogout.onReady();
    });
})(jQuery);
