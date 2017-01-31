jQuery(function($) {
  if( $('#ina_show_warn_message_only').is(":checked") ) {
    $('.show_on_warn_message_enabled').show();
  } else {
    $('.show_on_warn_message_enabled').hide();
  }

  $('#ina_show_warn_message_only').click(function() {
    if( $( this ).prop( "checked" )) {
      $('.show_on_warn_message_enabled').show();
    } else {
      $('.show_on_warn_message_enabled').hide();
    }
  });

  // Add Color Picker to all inputs that have 'color-field' class
  $( '.ina_color_picker' ).wpColorPicker();

  if( $('input[name="ina_full_overlay"]').is(":checked") ) {
    $('.ina_colorpicker_show').show();
  } else {
    $('.ina_colorpicker_show').hide();
  }

  $('input[name="ina_full_overlay"]').click(function(){
    if( $( this ).prop( "checked" )) {
      $('.ina_colorpicker_show').show();
    } else {
      $('.ina_colorpicker_show').hide();
    }
  });
});