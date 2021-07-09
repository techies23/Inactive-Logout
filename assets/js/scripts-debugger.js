"use strict";

function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { Promise.resolve(value).then(_next, _throw); } }

function _asyncToGenerator(fn) { return function () { var self = this, args = arguments; return new Promise(function (resolve, reject) { var gen = fn.apply(self, args); function _next(value) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value); } function _throw(err) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err); } _next(undefined); }); }; }

jQuery(function ($) {
  var ina_debugger = {
    init: function init() {
      this.cacheElem();
      this.evntLoaders();
      this.inactive_logout_tabID = '';
    },
    cacheElem: function cacheElem() {
      this.timeoutID = null;
      this.timeoutMessage = null;
      this.timeout = ina_ajax.settings.timeout;
      this.timeout_defined = this.timeout;
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
    resetTimer: function () {
      var _resetTimer = _asyncToGenerator( /*#__PURE__*/regeneratorRuntime.mark(function _callee() {
        return regeneratorRuntime.wrap(function _callee$(_context) {
          while (1) {
            switch (_context.prev = _context.next) {
              case 0:
                clearTimeout(this.timeoutID);
                clearInterval(this.timeoutMessage);
                this.timeout_defined = this.timeout;
                _context.prev = 3;
                _context.next = 6;
                return this.startTimer();

              case 6:
                _context.next = 11;
                break;

              case 8:
                _context.prev = 8;
                _context.t0 = _context["catch"](3);

                if (_context.t0 instanceof TypeError) {
                  console.log(_context.t0, true);
                } else {
                  console.log(_context.t0, true);
                }

              case 11:
              case "end":
                return _context.stop();
            }
          }
        }, _callee, this, [[3, 8]]);
      }));

      function resetTimer() {
        return _resetTimer.apply(this, arguments);
      }

      return resetTimer;
    }(),
    startTimer: function startTimer() {
      var _this = this;

      $('.coutdown-timer').html('Waiting for Inactivity...');
      return new Promise(function (resolve) {
        _this.timeoutID = setTimeout(_this.goInactive.bind(_this), 5000);
      });
    },
    goInactive: function goInactive() {
      var that = this;
      this.timeoutMessage = setInterval(function () {
        that.timeout_defined--;

        if (that.timeout_defined > 0) {
          $('.coutdown-timer').html('<strong>Countdown to Logout:</strong> ' + that.secondsToHms(that.timeout_defined));
        } else {
          $('.coutdown-timer').html('Initiating Logout !');
        }
      }, 1000);
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
  ina_debugger.init();
});