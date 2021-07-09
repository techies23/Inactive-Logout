jQuery(function ($) {

    var ina_debugger = {
        init: function () {
            this.cacheElem();
            this.evntLoaders();
            this.inactive_logout_tabID = '';
        },

        cacheElem: function () {
            this.timeoutID = null;
            this.timeoutMessage = null;
            this.timeout = ina_ajax.settings.timeout;
            this.timeout_defined = this.timeout;
            this.countdown = 0;
            this.ajax_url = ina_ajax.ajaxurl;
        },

        evntLoaders: function () {
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
        resetTimer: async function () {
            clearTimeout(this.timeoutID);
            clearInterval(this.timeoutMessage);
            this.timeout_defined = this.timeout;

            try {
                await this.startTimer();
            } catch (e) {
                if (e instanceof TypeError) {
                    console.log(e, true);
                } else {
                    console.log(e, true);
                }
            }
        },

        startTimer: function () {
            $('.coutdown-timer').html('Waiting for Inactivity...');
            return new Promise(resolve => {
                this.timeoutID = setTimeout(this.goInactive.bind(this), 5000);
            });
        },

        goInactive: function () {
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

        secondsToHms: function (d) {
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
