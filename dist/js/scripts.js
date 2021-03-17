/**
 * @author Deepen
 * @updated 1.9.7
 * @since 1.0.0
 *
 * This file contains core JS files for the plugin. Do not edit for you will be consumed by the dark side.
 */
(function ($) {
    var inactive_logout_timeoutID;
    var inactive_logout_tabID;
    var inactive_logout_timeoutMessage;
    var ina_timeout = ina_ajax.settings.timeout;
    var ina_timeout_defined = ina_timeout * 1000;
    var ina_messageBox = 0;
    var ina_setting_countdown;
    var ina_countdown = 10;
    var ina_ajax_url = ina_ajax.ajaxurl;
    var inactiveLogout = {

        onReady: function () {
            this.setupDOM();
            this.eventListeners();
        },

        setupDOM: function () {
        },

        eventListeners: function () {
            //First get the broswer id
            inactive_logout_tabID = sessionStorage.inactive_logout_tabID && sessionStorage.closedLastTab !== '2' ? sessionStorage.inactive_logout_tabID : sessionStorage.inactive_logout_tabID = Math.random();
            sessionStorage.closedLastTab = '2';

            window.addEventListener('beforeunload', function (e) {
                sessionStorage.closedLastTab = '1';
            });
            localStorage.setItem("ina__browserTabID", inactive_logout_tabID);

            $(document).on("mousemove", this.resetTimer.bind(this));
            $(document).on("mousedown", this.resetTimer.bind(this));
            $(document).on("keydown", this.resetTimer.bind(this));
            $(document).on("DOMMouseScroll", this.resetTimer.bind(this));
            $(document).on("mousewheel", this.resetTimer.bind(this));
            $(document).on("touchmove", this.resetTimer.bind(this));
            $(document).on("MSPointerMove", this.resetTimer.bind(this));
            $(document).ready(this.resetTimer.bind(this));
            $(document).on("click", '.ina_stay_logged_in', this.stayLoggedInWarnMsg);
            $(document).on('click', '.btn-close-without-reload', this.stayLoggedInWarnMsg);

            this.startTimer();
        },

        //Starting timeout timer to go into inactive state after 11 seconds if any event like mousemove is not triggered
        startTimer: function () {
            inactive_logout_timeoutID = setTimeout(inactiveLogout.goInactive.bind(this), 11000);
        },

        //Resetting the timer
        resetTimer: function () {
            clearTimeout(inactive_logout_timeoutID);
            clearTimeout(inactive_logout_timeoutMessage);
            localStorage.setItem("ina__browserTabID", inactive_logout_tabID);

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
            var that = this;
            if (ina_messageBox === 0) {
                var browserTabID = localStorage.getItem("ina__browserTabID");
                if (parseFloat(browserTabID) === parseFloat(inactive_logout_tabID)) {
                    var dateTime = Date.now();
                    var timestamp = Math.floor(dateTime / 1000);

                    //Update Last Active Status
                    var postData = {action: 'ina_checklastSession', do: 'ina_updateLastSession', security: ina_ajax.ina_security, timestamp: timestamp};
                    $.post(ina_ajax_url, postData).done(function (response) {
                        if (response.success === false) {
                            return;
                        }

                        var elem = document.activeElement;
                        //If active element is in iframe or media uploader
                        if (elem && (elem.tagName === 'IFRAME' || elem.classList.contains('media-modal'))) {
                            that.resetTimer();
                            return false;
                        }

                        inactive_logout_timeoutMessage = setTimeout(function () {
                            inactiveLogout.showTimeoutMessage(response);
                        }, ina_timeout_defined);
                    });
                }
            }
        },

        //Show timeout Message Now
        showTimeoutMessage: function (response) {
            var t;
            var ina_disable_countdown = ina_ajax.settings.disable_countdown;
            var ina_warn_message_enabled = ina_ajax.settings.warn_message_enabled;

            document.onkeydown = function (evt) {
                var keycode = evt.charCode || evt.keyCode;
                //Disable all keys except F5
                if (keycode != 116) return false;
            };

            //Disable Right Click
            window.oncontextmenu = function () {
                return false;
            };

            ina_messageBox = 1;
            if (ina_warn_message_enabled) {
                //Only show message
                $('#ina__dp_logout_message_box').show().html(response.html);
            } else if (ina_disable_countdown) {
                //Disabled Countdown but directly logout
                var postData = {action: 'ina_checklastSession', do: 'ina_logout', security: ina_ajax.ina_security};
                $.post(ina_ajax_url, postData).done(function (op) {
                    if (op.redirect_url) {
                        $('#ina__dp_logout_message_box').show().html(response.html);
                        $('#ina__dp_logout_message_box .ina-dp-noflict-modal-body').html('<p>' + op.msg + '<p>');

                        //Logout Now
                        inactiveLogout.logout_now(op.redirect_url);
                    } else {
                        var is_admin = ina_ajax.is_admin ? '<a class="btn-close-without-reload" style="margin-left:10px;" href="javascript:void(0);">' + ina_ajax.i10n.close + '</a>' : '';
                        $('#ina__dp_logout_message_box').show().html(response.html);
                        $('#ina__dp_logout_message_box .ina-dp-noflict-modal-body').html('<p>' + op.msg + '</p><p class="ina-dp-noflict-btn-container"><a class="btn-timeout" href="javascript:void(0);" onclick="window.location.reload();">' + ina_ajax.i10n.ok + '</a>' + is_admin + '</p>');

                        //Logout Now
                        inactiveLogout.logout_now(false);
                    }
                    return false;
                });
            } else {
                ina_countdown = 10;
                $('#ina__dp_logout_message_box').show().html(response.html);
                ina_setting_countdown = setInterval(function () {
                    if (ina_countdown >= 0) {
                        t = ina_countdown--;
                        $(".ina_countdown").html('(' + t + ')');
                    }

                    if (t === 0) {
                        clearTimeout(ina_setting_countdown);
                        var postData = {action: 'ina_checklastSession', do: 'ina_logout', security: ina_ajax.ina_security};
                        $.post(ina_ajax_url, postData).done(function (op) {
                            if (op.redirect_url) {
                                $('#ina__dp_logout_message_box .ina-dp-noflict-modal-body').html('<p>' + op.msg + '<p>');

                                //Logout Now
                                inactiveLogout.logout_now(op.redirect_url);
                            } else {
                                var is_admin = ina_ajax.is_admin ? '<a class="btn-close-without-reload" style="margin-left:10px;" href="javascript:void(0);">' + ina_ajax.i10n.close + '</a>' : '';

                                $('#ina__dp_logout_message_box .ina-dp-noflict-modal-body').html('<p>' + op.msg + '</p><p class="ina-dp-noflict-btn-container"><a class="btn-timeout" href="javascript:void(0);" onclick="window.location.reload();">' + ina_ajax.i10n.ok + '</a>' + is_admin + '</p>');

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
            $('#ina__dp_logout_message_box').hide();
            $(".ina_countdown").text('');
        },

        /**
         * Logout User Here
         * @param redirect_url
         */
        logout_now: function (redirect_url) {
            var logoutData = {action: 'ina_logout_session', security: ina_ajax.ina_security};
            $.post(ina_ajax_url, logoutData).done(function () {
                clearTimeout(inactive_logout_timeoutID);
                localStorage.removeItem('ina__browserTabID');
                if (redirect_url) {
                    window.location = redirect_url;
                }

                if (ina_ajax.is_admin) {
                    //Trigger Hearbeat API wp auth check
                    $(document).trigger('heartbeat-tick.wp-auth-check', [{'wp-auth-check': false}]);
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
