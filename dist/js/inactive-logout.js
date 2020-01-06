/**
 * @author Deepen
 * @updated 1.9.0
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
    var ina_messageBox = 0;
    var ina_dom = {};
    var ina_setting_countdown;
    var ina_countdown = 10;

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
            $(document).on("mousemove", inactiveLogout.resetTimer);
            $(document).on("mousedown", inactiveLogout.resetTimer);
            $(document).on("keydown", inactiveLogout.resetTimer);
            $(document).on("DOMMouseScroll", inactiveLogout.resetTimer);
            $(document).on("mousewheel", inactiveLogout.resetTimer);
            $(document).on("touchmove", inactiveLogout.resetTimer);
            $(document).on("MSPointerMove", inactiveLogout.resetTimer);
            $(window).on("load", inactiveLogout.resetTimer);

            ina_dom.stayLoggedIn.on("click", inactiveLogout.stayLoggedInWarnMsg);

            //First get the broswer id
            inactive_logout_tabID = sessionStorage.inactive_logout_tabID && sessionStorage.closedLastTab !== '2' ? sessionStorage.inactive_logout_tabID : sessionStorage.inactive_logout_tabID = Math.random();
            sessionStorage.closedLastTab = '2';
            $(window).on('unload beforeunload', function () {
                sessionStorage.closedLastTab = '1';
            });
            localStorage.setItem("ina__browserTabID", inactive_logout_tabID);

            this.startTimer();
        },

        //Starting timeout timer to go into inactive state after 11 seconds if any event like mousemove is not triggered
        startTimer: function () {
            inactive_logout_timeoutID = setTimeout(inactiveLogout.goInactive, 11000);
        },

        //Resetting the timer
        resetTimer: function () {
            clearTimeout(inactive_logout_timeoutID);
            clearTimeout(inactive_logout_timeoutMessage);
            localStorage.setItem("ina__browserTabID", inactive_logout_tabID);

            try {
                inactiveLogout.startTimer();
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
                    //If active element is in iframe or media uploader
                    if (elem && (elem.tagName === 'IFRAME' || elem.classList.contains('media-modal'))) {
                        inactiveLogout.resetTimer();
                        return false;
                    }

                    var browserTabID = localStorage.getItem("ina__browserTabID");
                    if (browserTabID === inactive_logout_tabID) {
                        inactive_logout_timeoutMessage = setTimeout(inactiveLogout.showTimeoutMessage, ina_timeout_defined);
                    }
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
                        ina_dom.msg_boxBody.html('<p>' + op.msg + '</p><p class="ina-dp-noflict-btn-container"><a class="btn-timeout" href="javascript:void(0);" onclick="window.location.reload();">OK</a></p>');

                        //Logout Now
                        inactiveLogout.logout_now(false);
                    }
                    return false;
                });
            } else {
                ina_countdown = 10;
                ina_dom.msg_box.show();
                ina_setting_countdown = setInterval(function () {
                    if (ina_countdown >= 0) {
                        t = ina_countdown--;
                        ina_dom.countdown.html('(' + t + ')');
                    }

                    if (t === 0) {
                        clearTimeout(ina_setting_countdown);
                        var postData = {action: 'ina_checklastSession', do: 'ina_logout', security: ina_ajax.ina_security};
                        $.post(ina_ajax.ajaxurl, postData).done(function (op) {
                            if (op.redirect_url) {
                                ina_dom.msg_boxBody.html('<p>' + op.msg + '<p>');

                                //Logout Now
                                inactiveLogout.logout_now(op.redirect_url);
                            } else {
                                ina_dom.msg_boxBody.html('<p>' + op.msg + '</p><p class="ina-dp-noflict-btn-container"><a class="btn-timeout" href="javascript:void(0);" onclick="window.location.reload();">OK</a></p>');

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
            clearTimeout(ina_setting_countdown);
            ina_countdown = 10;
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
                clearTimeout(inactive_logout_timeoutID);

                if (redirect_url) {
                    localStorage.removeItem('ina__browserTabID');
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
