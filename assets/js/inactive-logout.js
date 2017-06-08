var timeoutID;
var tabID;
var timeoutMessage;
var ina_timeout = jQuery('meta[name=ina_timeout]').attr('content');
var timeout_defined = ina_timeout * 1000; //Minutes
var messageBox = 0;
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

  startTimer();
}
setup();

//Starting timeout timer to go into inactive state after 15 seconds if any event like mousemove is not triggered
function startTimer() {
  timeoutID = window.setTimeout(goInactive, 15000);
}

//Resetting the timer
function resetTimer(e) {
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
    var dateTime = Date.now();
    var timestamp = Math.floor(dateTime / 1000);

    jQuery(document).ready(function($) {
      //Update Last Active Status
      var postData = { action: 'ina_checklastSession', do: 'ina_updateLastSession', security: ina_ajax.ina_security, timestamp: timestamp };
      $.post( ina_ajax.ajaxurl, postData ).done(function(response) {
        console.log("Last Active on: " + Date.now());
        var browserTabID = localStorage.getItem("ina__browserTabID");
        if( browserTabID == tabID ) {
          timeoutMessage = window.setTimeout(showTimeoutMessage, timeout_defined);
        }
      });
    });
  }
}

//Show timeout Message Now
function showTimeoutMessage() {
  var countdown = 10;
  var t;
  var ina_disable_countdown = jQuery('meta[name=ina_disable_countdown]').attr('content');
  var ina_warn_message_enabled = jQuery('meta[name=ina_warn_message_enabled]').attr('content');
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
      var postData = { action: 'ina_checklastSession', do: 'ina_logout', security: ina_ajax.ina_security };
      $.post( ina_ajax.ajaxurl, postData).done(function(response) {
        var op = $.parseJSON(response);
        if( op.redirect_url ) {
          window.location = op.redirect_url;
        } else {
          $('#ina__dp_logout_message_box .ina-dp-noflict-modal-body').html( '<p>' + op.msg + '<p><p class="ina-dp-noflict-btn-container"><a class="btn-timeout" href="javascript:void(0);" onclick="window.location.reload();">OK</a></p>' );
        }
        return false;
      });
    } else {
      $('#ina__dp_logout_message_box').show();
      setting_countdown = setInterval(function() {
        if( countdown >= 0 ) {
          t = countdown--;
          $(".ina_countdown").html( '(' + t + ')' );
        }

        if( t == 0 ) {
          clearTimeout(setting_countdown);
          var postData = { action: 'ina_checklastSession', do: 'ina_logout', security: ina_ajax.ina_security };
          $.post( ina_ajax.ajaxurl, postData).done(function(response) {
            var op = $.parseJSON(response);
            if( op.redirect_url ) {
              window.location = op.redirect_url;
            } else {
              $('#ina__dp_logout_message_box .ina-dp-noflict-modal-body').html( '<p>' + op.msg + '<p><p class="ina-dp-noflict-btn-container"><a class="btn-timeout" href="javascript:void(0);" onclick="window.location.reload();">OK</a></p>' );
            }
            return false;
          });
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