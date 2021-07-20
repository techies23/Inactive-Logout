"use strict";

/**
 * @author Deepen
 * @updated 1.9.7
 * @since 1.0.0
 *
 * This file contains core JS files for the plugin. Do not edit for you will be consumed by the dark side.
 */
(function ($) {
  var inactive_logout_timeoutID;
  var inactive_logout_timeoutMessage;
  var ina_timeout = ina_ajax.settings.timeout;
  var ina_timeout_defined = ina_timeout * 1000;
  var ina_messageBox = 0;
  var ina_setting_countdown;
  var ina_countdown = ina_ajax.settings.countdown_timeout;
  var ina_ajax_url = ina_ajax.ajaxurl;
  var inactiveLogout = {
    onReady: function onReady() {
      this.inactive_logout_tabID = '';
      this.isActiveTab = true;
      this.eventListeners();
      this.startTimer();
    },
    eventListeners: function eventListeners() {
      //First get the broswer id
      this.inactive_logout_tabID = sessionStorage.inactive_logout_tabID && sessionStorage.closedLastTab !== '2' ? sessionStorage.inactive_logout_tabID : sessionStorage.inactive_logout_tabID = Math.random();
      sessionStorage.closedLastTab = '2';
      window.addEventListener('beforeunload', function (e) {
        sessionStorage.closedLastTab = '1';
      });
      localStorage.setItem("ina__browserTabID", this.inactive_logout_tabID);
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
    },
    //Starting timeout timer to go into inactive state after 5 seconds if any event like mousemove is not triggered
    startTimer: function startTimer() {
      inactive_logout_timeoutID = setTimeout(inactiveLogout.goInactive.bind(this), 5000);
    },
    //Resetting the timer
    resetTimer: function resetTimer() {
      clearTimeout(inactive_logout_timeoutID);
      clearTimeout(inactive_logout_timeoutMessage);
      localStorage.setItem("ina__browserTabID", this.inactive_logout_tabID);

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
    goInactive: function goInactive() {
      var that = this;

      if (ina_messageBox === 0) {
        var browserTabID = localStorage.getItem("ina__browserTabID");

        if (parseFloat(browserTabID) === parseFloat(that.inactive_logout_tabID)) {
          var dateTime = Date.now();
          var timestamp = Math.floor(dateTime / 1000); //Update Last Active Status

          var postData = {
            action: 'ina_checklastSession',
            "do": 'ina_updateLastSession',
            security: ina_ajax.ina_security,
            timestamp: timestamp
          };
          $.post(ina_ajax_url, postData).done(function (response) {
            if (response.success === false) {
              return;
            }

            var elem = document.activeElement; //If active element is in iframe or media uploader

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
    showTimeoutMessage: function showTimeoutMessage(response) {
      var t;
      var ina_disable_countdown = ina_ajax.settings.disable_countdown;
      var ina_warn_message_enabled = ina_ajax.settings.warn_message_enabled;

      document.onkeydown = function (evt) {
        var keycode = evt.charCode || evt.keyCode; //Disable all keys except F5

        if (keycode != 116) return false;
      }; //Disable Right Click


      window.oncontextmenu = function () {
        return false;
      };

      ina_messageBox = 1;

      if (ina_warn_message_enabled) {
        //Only show message
        $('#ina__dp_logout_message_box').show().html(response.html);
      } else if (ina_disable_countdown) {
        //Disabled Countdown but directly logout
        var postData = {
          action: 'ina_checklastSession',
          "do": 'ina_logout',
          security: ina_ajax.ina_security
        };
        $.post(ina_ajax_url, postData).done(function (op) {
          if (op.redirect_url) {
            $('#ina__dp_logout_message_box').show().html(response.html);
            $('#ina__dp_logout_message_box .ina-dp-noflict-modal-body').html('<p>' + op.msg + '<p>'); //Logout Now

            inactiveLogout.logout_now(op.redirect_url);
          } else {
            $('#ina__dp_logout_message_box').show().html(response.html);

            if (ina_ajax.settings.disable_login) {
              $('#ina__dp_logout_message_box .ina-dp-noflict-modal-body').html('<p>' + op.msg + '</p><p class="ina-dp-noflict-btn-container"><a class="btn-timeout" href="javascript:void(0);" onclick="window.location.reload();">' + ina_ajax.i10n.ok + '</a><a class="btn-close-without-reload" style="margin-left:10px;" href="javascript:void(0);">' + ina_ajax.i10n.close + '</a></p>');
            } else {
              $('#ina__dp_logout_message_box .ina-dp-noflict-modal-body').html('<p>' + op.msg + '</p>');
            } //Logout Now


            inactiveLogout.logout_now(false);
          }

          return false;
        });
      } else {
        ina_countdown = ina_ajax.settings.countdown_timeout;
        $('#ina__dp_logout_message_box').show().html(response.html);
        ina_setting_countdown = setInterval(function () {
          if (ina_countdown >= 0) {
            t = ina_countdown--;
            $(".ina_countdown").html('(' + inactiveLogout.secondsToHms(t) + ')');
          }

          if (t === 0) {
            clearTimeout(ina_setting_countdown);
            var postData = {
              action: 'ina_checklastSession',
              "do": 'ina_logout',
              security: ina_ajax.ina_security
            };
            $.post(ina_ajax_url, postData).done(function (op) {
              if (op.redirect_url) {
                $('#ina__dp_logout_message_box .ina-dp-noflict-modal-body').html('<p>' + op.msg + '<p>'); //Logout Now

                inactiveLogout.logout_now(op.redirect_url);
              } else {
                if (ina_ajax.settings.disable_login) {
                  $('#ina__dp_logout_message_box .ina-dp-noflict-modal-body').html('<p>' + op.msg + '</p><p class="ina-dp-noflict-btn-container"><a class="btn-timeout" href="javascript:void(0);" onclick="window.location.reload();">' + ina_ajax.i10n.ok + '</a><a class="btn-close-without-reload" style="margin-left:10px;" href="javascript:void(0);">' + ina_ajax.i10n.close + '</a></p>');
                } else {
                  $('#ina__dp_logout_message_box .ina-dp-noflict-modal-body').html('<p>' + op.msg + '</p>');
                } //Logout Now


                inactiveLogout.logout_now(false);
              }

              return false;
            });
          }
        }, 1000);
      }
    },
    stayLoggedInWarnMsg: function stayLoggedInWarnMsg() {
      document.onkeydown = function (evt) {
        return true;
      };

      window.oncontextmenu = null;
      clearTimeout(ina_setting_countdown);
      ina_countdown = ina_ajax.settings.countdown_timeout;
      ina_messageBox = 0;
      $('#ina__dp_logout_message_box').hide();
      $(".ina_countdown").text('');
    },

    /**
     * Logout User Here
     * @param redirect_url
     */
    logout_now: function logout_now(redirect_url) {
      var that = this;
      var logoutData = {
        action: 'ina_logout_session',
        security: ina_ajax.ina_security
      };
      $.post(ina_ajax_url, logoutData).done(function (logout_response) {
        clearTimeout(inactive_logout_timeoutID);
        localStorage.removeItem('ina__browserTabID');

        if (redirect_url) {
          setTimeout(function () {
            window.location = redirect_url;
          }, 1000);
        }

        if (ina_ajax.is_admin && ina_ajax.settings.disable_login) {
          //Trigger Hearbeat API wp auth check
          $(document).trigger('heartbeat-tick.wp-auth-check', [{
            'wp-auth-check': false
          }]);
        }

        document.onkeydown = function (evt) {
          return true;
        }; //Hide debugger if enabled


        if ($('.ina-debugger-section').length > 0) {
          $('.ina-debugger-section').hide();
        } // Perform AJAX login on form submit


        $('form#ina-ajaxlogin-form').on('submit', function (e) {
          $('.ina-login-status').show().text("* " + ina_ajax.i10n.login_wait + "...").css('color', '#2b8605');
          $.ajax({
            type: 'POST',
            dataType: 'json',
            url: ina_ajax.ajaxurl,
            data: {
              'action': 'ina_ajaxlogin',
              'username': $('form#ina-ajaxlogin-form #ina-username').val(),
              'password': $('form#ina-ajaxlogin-form #ina-password').val(),
              'nonce': logout_response.nonce
            },
            success: function success(response) {
              if (response.success === true) {
                $('.ina-login-status').html(response.data.message).css('color', '#2b8605'); //Show debugger if enabled

                if ($('.ina-debugger-section').length > 0) {
                  $('.ina-debugger-section').show();
                }

                that.stayLoggedInWarnMsg();
              } else {
                $('.ina-login-status').html(response.data.message);
              }
            }
          });
          e.preventDefault();
        }); //Logged Out

        console.log("Session Logged Out !!");
      });
    },
    secondsToHms: function secondsToHms(d) {
      d = Number(d);
      var h = Math.floor(d / 3600);
      var m = Math.floor(d % 3600 / 60);
      var s = Math.floor(d % 3600 % 60);
      var hDisplay = h > 0 ? h + (h == 1 ? " " + ina_ajax.i10n.hour + ", " : " " + ina_ajax.i10n.hours + ", ") : "";
      var mDisplay = m > 0 ? m + (m == 1 ? " " + ina_ajax.i10n.minute + ", " : " " + ina_ajax.i10n.minutes + ", ") : "";
      var sDisplay = s > 0 ? s + (s == 1 ? " " + ina_ajax.i10n.second + "" : " " + ina_ajax.i10n.seconds + "") : "";
      return hDisplay + mDisplay + sDisplay;
    }
  }; //Debugger JS

  var ina_debugger = {
    init: function init() {
      this.cacheElem();
      this.evntLoaders();
      this.startTimer();
    },
    cacheElem: function cacheElem() {
      this.timeoutID = null;
      this.timeoutMessage = null;
      this.timeout = ina_timeout;
      this.timeout_defined = this.timeout;
      this.countdown = 0;
      this.ajax_url = ina_ajax.ajaxurl;
      this.debuggerState = localStorage.getItem('ina__debuggerWindow');
      this.mainDebuggerWrapper = $('.ina-debugger-section');
    },
    evntLoaders: function evntLoaders() {
      $(document).on("mousemove", this.resetTimer.bind(this));
      $(document).on("mousedown", this.resetTimer.bind(this));
      $(document).on("keydown", this.resetTimer.bind(this));
      $(document).on("DOMMouseScroll", this.resetTimer.bind(this));
      $(document).on("mousewheel", this.resetTimer.bind(this));
      $(document).on("touchmove", this.resetTimer.bind(this));
      $(document).on("MSPointerMove", this.resetTimer.bind(this));
      $('.ina-debugger-section-btn-close').on('click', this.closeDebuggerWindow.bind(this)); //Stay closed on reload if debugger is closed by user action.

      if (this.debuggerState === "closed") {
        this.mainDebuggerWrapper.css({
          width: '10px',
          padding: '20px',
          height: '180px'
        });
        this.mainDebuggerWrapper.find('ul').hide();
        $('.ina-debugger-section-btn-close').data('state', 'open').removeClass('ina-debugger-section-btn-close').addClass('ina-debugger-section-btn-open');
      }
    },
    closeDebuggerWindow: function closeDebuggerWindow(e) {
      var state = $(e.currentTarget).data('state');
      var mainWrapper = this.mainDebuggerWrapper;

      if (state === "close") {
        mainWrapper.css({
          width: '10px',
          padding: '20px',
          height: '180px'
        });
        mainWrapper.find('ul').hide();
        $(e.currentTarget).data('state', 'open').removeClass('ina-debugger-section-btn-close').addClass('ina-debugger-section-btn-open');
        localStorage.setItem("ina__debuggerWindow", 'closed');
      } else {
        mainWrapper.css({
          width: 'auto',
          padding: '20px 80px 20px 20px',
          height: 'auto'
        });
        mainWrapper.find('ul').show();
        $(e.currentTarget).data('state', 'close').removeClass('ina-debugger-section-btn-open').addClass('ina-debugger-section-btn-close');
        localStorage.removeItem("ina__debuggerWindow");
      }
    },
    //Resetting the timer
    resetTimer: function resetTimer() {
      clearTimeout(this.timeoutID);
      clearInterval(this.timeoutMessage);
      this.timeout_defined = this.timeout;

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
    startTimer: function startTimer() {
      $('.coutdown-timer').html('Waiting for Inactivity...');
      this.timeoutID = setTimeout(this.goInactive.bind(this), 5000);
    },
    goInactive: function goInactive() {
      var that = this;
      var browserTabID = localStorage.getItem("ina__browserTabID");

      if (parseFloat(browserTabID) === parseFloat(inactiveLogout.inactive_logout_tabID)) {
        this.timeoutMessage = setInterval(function () {
          that.timeout_defined--;

          if (that.timeout_defined > 0) {
            $('.coutdown-timer').html('<strong>Countdown to Logout:</strong> ' + that.secondsToHms(that.timeout_defined));
          } else {
            $('.coutdown-timer').html('Initiating Logout !');
          }
        }, 1000);
      }
    },
    secondsToHms: function secondsToHms(d) {
      d = Number(d);
      var h = Math.floor(d / 3600);
      var m = Math.floor(d % 3600 / 60);
      var s = Math.floor(d % 3600 % 60);
      var hDisplay = h > 0 ? h + (h == 1 ? " " + ina_ajax.i10n.hour + ", " : " " + ina_ajax.i10n.hours + ", ") : "";
      var mDisplay = m > 0 ? m + (m == 1 ? " " + ina_ajax.i10n.minute + ", " : " " + ina_ajax.i10n.minutes + ", ") : "";
      var sDisplay = s > 0 ? s + (s == 1 ? " " + ina_ajax.i10n.second + "" : " " + ina_ajax.i10n.seconds + "") : "";
      return hDisplay + mDisplay + sDisplay;
    }
  };
  $(function () {
    inactiveLogout.onReady();

    if (ina_ajax.settings.enable_debugger) {
      ina_debugger.init();
    }
  });
})(jQuery);