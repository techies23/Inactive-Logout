var timeoutID;
var tabID;
var timeoutMessage;
var ina_timeout = ina_meta_data.ina_timeout;
var ina_activityTimestamp;
var timeout_defined = ina_timeout * 1000; //Minutes
var messageBox = 0;
var ina_isLoggedIn = true;
function setup() {
  this.addEventListener("mousemove", resetTimer, false);
  this.addEventListener("mousedown", resetTimer, false);
  this.addEventListener("keypress", resetTimer, false);
  this.addEventListener("DOMMouseScroll", resetTimer, false);
  this.addEventListener("mousewheel", resetTimer, false);
  this.addEventListener("touchmove", resetTimer, false);
  this.addEventListener("MSPointerMove", resetTimer, false);

  //First get the broswer id
  tabID = sessionStorage.tabID && sessionStorage.closedLastTab !== '2' ? sessionStorage.tabID : sessionStorage.tabID = Math.random();
  sessionStorage.closedLastTab = '2';
  jQuery(window).on('unload beforeunload', function() {
    sessionStorage.closedLastTab = '1';
  });
  localStorage.setItem("ina__browserTabID", tabID);

  // Enable a constant heartbeat
  wp.heartbeat.disableSuspend();

  // send the last activity time with each heartbeat
  jQuery( document ).on( 'heartbeat-send', function ( event, data ) {
    if (!data.hasOwnProperty('inactive_logout')) {
      data['inactive_logout'] = [];
    }
    data['inactive_logout']['timestamp'] = ina_activityTimestamp;
  });


  // Heartbeat response listener
  jQuery(document).on('heartbeat-tick', function (event, data) {
    console.log("Last Active on: " + ina_activityTimestamp);
    var browserTabID = localStorage.getItem("ina__browserTabID");
    if( browserTabID == tabID ) {
      timeoutMessage = window.setTimeout(showTimeoutMessage, timeout_defined);
    }

    // make a note of whether the user is currently logged in
    if (data.hasOwnProperty('wp-auth-check')) {
      var wasLoggedIn = ina_isLoggedIn;
      ina_isLoggedIn = data['wp-auth-check'];

      if (wasLoggedIn && !ina_isLoggedIn) {
        console.log("You have been logged out.");
      }
      if (!wasLoggedIn && ina_isLoggedIn) {
        console.log("You have logged in.");
      }

      if (!wasLoggedIn && ina_isLoggedIn && messageBox) {
        jQuery('#ina__dp_logout_message_box').hide();
        window.oncontextmenu = null;
        clearTimeout(setting_countdown);
        countdown = 10;
        messageBox = 0;
        resetTimer();
        wp.heartbeat.interval('fast');
      }
    }

    // respond to actions
    if (data.hasOwnProperty('inactive_logout')) {
      var logged_out = data['inactive_logout']['logged_out'];
      if (logged_out) {
        clearTimeout(setting_countdown);
        var redirect_url = data['inactive_logout']['redirect_url'];
        if( redirect_url ) {
          window.location = redirect_url;
        } else {
          var msg = data['inactive_logout']['msg'];
          $('#ina__dp_logout_message_box .ina-dp-noflict-modal-body').html( '<p>' + msg + '<p><p class="ina-dp-noflict-btn-container"><a class="btn-timeout" href="javascript:void(0);" onclick="ina__timeout_ok();">Reload</a></p>' );
          wp.heartbeat.interval('fast');
        }
      }
    }
  });

  startTimer();
}
setup();

//Starting timeout timer to go into inactive state after 15 seconds if any event like mousemove is not triggered
function startTimer() {
  timeoutID = window.setTimeout(goInactive, 15000);
}

//Resetting the timer
function resetTimer(e) {
  var dateTime = Date.now();
  ina_activityTimestamp = Math.floor(dateTime / 1000);

  window.clearTimeout(timeoutID);
  window.clearTimeout(timeoutMessage);
  localStorage.setItem("ina__browserTabID", tabID);
  goActive();
}

/**
* User is inactive now save last session activity time here
*/
function goInactive() {
  if( messageBox == 0 ) {
    // var dateTime = Date.now();
    // var timestamp = Math.floor(dateTime / 1000);

    // jQuery(document).ready(function($) {
      //Update Last Active Status
      wp.heartbeat.enqueue(
        'inactive_logout',
        {
          do: 'ina_updateLastSession',
          security: ina_ajax.ina_security,
          timestamp: ina_activityTimestamp
        },
        false
      );
      // wp.heartbeat.scheduleNextTick();
      // DO NOT connect immediately, we can afford to let heartbeat tick in its own time
      /*
      var postData = { action: 'ina_checklastSession', do: 'ina_updateLastSession', security: ina_ajax.ina_security, timestamp: timestamp };
      $.post( ina_ajax.ajaxurl, postData ).done(function(response) {
        console.log("Last Active on: " + Date.now());
        var browserTabID = localStorage.getItem("ina__browserTabID");
        if( browserTabID == tabID ) {
          timeoutMessage = window.setTimeout(showTimeoutMessage, timeout_defined);
        }
      });
      */
    // });
  }
}

function ina__timeout_ok() {
  if (ina_isLoggedIn) {
    jQuery('#ina__dp_logout_message_box').hide();
    messageBox = 0;
    startTimer();
  } else {
    // trigger a heartbeart to check if the user's logged in
    // if we don't get a response in 1 second, reload the page anyway
    setTimeout(function () {
      if (messageBox && !ina_isLoggedIn) {
        window.location.reload();
      }
    }, 1000); // 1 second
    wp.heartbeat.connectNow();
  }
}

//Show timeout Message Now
function showTimeoutMessage() {
  var countdown = 10;
  var t;
  var ina_disable_countdown = ina_meta_data.ina_disable_countdown;
  var ina_warn_message_enabled = ina_meta_data.ina_warn_message_enabled;
  jQuery(function($) {
    document.onkeydown = function (evt) {
      var keycode = evt.charCode || evt.keyCode;
      //Disable all keys except F5
      if(keycode != 116) return false;
    }

    //Disable Right Click
    window.oncontextmenu = function () {
      return false;
    }

    var ina_popup_bg_enalbed = $('.ina__no_confict_popup_bg').data('bgenabled');
    if( ina_popup_bg_enalbed ) {
      var ina_popup_bg = $('.ina__no_confict_popup_bg').data('bg');
      $('#ina__dp_logout_message_box').css('background', ina_popup_bg);
    }

    messageBox = 1;
    if( ina_warn_message_enabled ) {
      //Only show message
      $('#ina__dp_logout_message_box').show();
      $('.ina_stay_logged_in').click(function() {
        document.onkeydown = function (evt) { return true; }
        window.oncontextmenu = null;
        $('#ina__dp_logout_message_box').hide();
        messageBox = 0;
      });
    } else if(ina_disable_countdown) {
      $('#ina__dp_logout_message_box').show();

      //Disabled Countdown but directly logout
      console.log("Logging out.");
      wp.heartbeat.enqueue(
        'inactive_logout',
        {
          do: 'ina_logout',
          security: ina_ajax.ina_security,
        },
        false
      );
      wp.heartbeat.connectNow();
      /*
      var postData = { action: 'ina_checklastSession', do: 'ina_logout', security: ina_ajax.ina_security };
      $.post( ina_ajax.ajaxurl, postData).done(function(op) {
        if( op.redirect_url ) {
          window.location = op.redirect_url;
        } else {
          $('#ina__dp_logout_message_box .ina-dp-noflict-modal-body').html( '<p>' + op.msg + '<p><p class="ina-dp-noflict-btn-container"><a class="btn-timeout" href="javascript:void(0);" onclick="ina__timeout_ok();">OK</a></p>' );
        }
        return false;
      });
      */
    } else {
      $('#ina__dp_logout_message_box').show();
      setting_countdown = setInterval(function() {
        if( countdown >= 0 ) {
          t = countdown--;
          $(".ina_countdown").html( '(' + t + ')' );
        }

        if( t == 0 ) {
          clearTimeout(setting_countdown);
          wp.heartbeat.enqueue(
            'inactive_logout',
            {
              do: 'ina_logout',
              security: ina_ajax.ina_security
            },
            false
          );
          wp.heartbeat.connectNow();
          /*
          var postData = { action: 'ina_checklastSession', do: 'ina_logout', security: ina_ajax.ina_security };
          $.post( ina_ajax.ajaxurl, postData).done(function(op) {
            if( op.redirect_url ) {
              window.location = op.redirect_url;
            } else {
              $('#ina__dp_logout_message_box .ina-dp-noflict-modal-body').html( '<p>' + op.msg + '<p><p class="ina-dp-noflict-btn-container"><a class="btn-timeout" href="javascript:void(0);" onclick="ina__timeout_ok();">OK</a></p>' );
            }
            return false;
          });
          */
        }
      }, 1000);

      $('.ina_stay_logged_in').click(function() {
        document.onkeydown = function (evt) { return true; }
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
function goActive() {
  startTimer();
}
