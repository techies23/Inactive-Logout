"use strict";

jQuery(function ($) {
  var ina_debugger = {
    init: function init() {
      this.cacheElem();
      this.evntLoaders();
    },
    cacheElem: function cacheElem() {
      this.timeoutID = null;
      this.tabID = null;
      this.timeoutMessage = null;
      this.timeout = ina_ajax.settings.timeout;
      this.timeout_defined = this.timeout;
      this.messageBox = 0;
      this.setting_countdown = null;
      this.countdown = 0;
      this.ajax_url = ina_ajax.ajaxurl;
    },
    evntLoaders: function evntLoaders() {
      $(document).on("mousemove", this.resetTimer.bind(this));
      $(document).on("mousedown", this.resetTimer.bind(this));
      $(document).on("keydown", this.resetTimer.bind(this));
      $(document).on("DOMMouseScroll", this.resetTimer.bind(this));
      $(document).on("mousewheel", this.resetTimer.bind(this));
      $(document).on("touchmove", this.resetTimer.bind(this));
      $(document).on("MSPointerMove", this.resetTimer.bind(this));
      this.startTimer();
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
      this.timeoutID = setTimeout(this.goInactive.bind(this), 11000);
    },
    goInactive: function goInactive() {
      var that = this;
      this.timeoutMessage = setInterval(function () {
        that.timeout_defined--;

        if (that.timeout_defined > 0) {
          $('.coutdown-timer').html('<strong>Countdown to Logout:</strong> ' + that.timeout_defined);
        } else {
          $('.coutdown-timer').html('Initiating Logout !');
        }
      }, 1000);
    }
  };
  ina_debugger.init();
});