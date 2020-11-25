jQuery(function ($) {
    var pointerContentIndex = 'ina_pointer_dialog';
    if (inactive_pointer.pointers.inactive_logout_intro_tour) {//If pointers are set, show them off
        var pointer = inactive_pointer.pointers.inactive_logout_intro_tour;

        /**
         * Create a pointer using content defined at pointer[pointerContentIndex]
         * and display it on a particular screen. The screen to display
         * the pointer on is defined at pointer[pointerContentIndex].screen
         *
         * @param pointerContentIndex
         */
        var generatePointer = function (pointerContentIndex) {
            var options = $.extend(pointer[pointerContentIndex].options, {
                close: function () {
                    //Change the active screen
                    //Add your custom ($) logic to change the plugin screen to the one corresponding to the content at pointerContentIndex
                    ////Remember, you specified the screen at pointer[pointerContentIndex].screen
                    //In my case, using $ tabs, I used this to switch to the correct tab:
                    // $( "#tabs" ).tabs( "option", "active", pointer[pointerContentIndex].screen );

                    //Disable tour mode
                    $.post(inactive_pointer.ajax_url, {
                        action: 'ina_disable_tour_mode'
                    });
                }
            });

            //Open the pointer
            $(pointer[pointerContentIndex].target).pointer(options).pointer('open');

            //Inject a "Next" button into the pointer
            // $('a.close').after('<a href="#" class="ina-pointer-plugin-next button-primary">Next</a>');
        };

        generatePointer(pointerContentIndex);

        //Move to the next pointer when 'Next' is clicked
        //Event needs to be attached this way since the link was manually injected into the HTML
        $('body').on('click', 'a.ina-pointer-plugin-next', function (e) {
            e.preventDefault();
            //Manually hide the current pointer. We don't close it because if we do, the 'close' function,
            //which also disables tour mode, would be called
            $(this).parents('.wp-pointer').hide();
            if (pointerContentIndex < pointer.length) {
                ++pointerContentIndex;
            } else {
                //End of the tour
                //Dismiss the pointer in the WP db
                //Disable tour mode
                $.post(inactive_pointer.ajax_url, {
                    action: 'ina_disable_tour_mode'
                });
                return;
            }

            //Open the next pointer
            generatePointer(pointerContentIndex);
        });
    }
});