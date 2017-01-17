<!--START INACTIVE LOGOUT MODAL CONTENT-->
<?php $ina_warn_message_enabled = get_option( '__ina_warn_message_enabled' ); 
if( $ina_warn_message_enabled == 1 ) { ?>
<div id="ina_logout_message_box" class="ina-modal">
	<div class="ina-modal-content">
		<div class="ina-modal-body ina-wakeup">
			<?php 
			$message_content = get_option( '__ina_warn_message' ); 
			$time = get_option( '__ina_logout_time' );
			$ina_helpers = Inactive__logout__Helpers::instance();
			$replaced_content = str_replace('{wakup_timout}', $ina_helpers->ina_convertToMinutes($time), $message_content);
			echo apply_filters( 'the_content', $replaced_content ); ?>
			<p class="ina-btn-container"><a class="button button-primary ina_stay_logged_in" href="javascript:void(0);"><?php _e('Continue', 'ina-logout'); ?></a></p>
		</div>
	</div>
</div>
<?php } else { ?>
<div id="ina_logout_message_box" class="ina-modal">
	<div class="ina-modal-content">
		<div class="ina-modal-header">
			<h3><?php _e('Session Timeout', 'ina-logout'); ?></h3>
		</div>
		<div class="ina-modal-body">
			<?php $message_content = get_option( '__ina_logout_message' ); ?>
			<?php echo apply_filters( 'the_content', $message_content ); ?>
			<p class="ina-btn-container"><a class="button button-primary ina_stay_logged_in" href="javascript:void(0);"><?php _e('Continue', 'ina-logout'); ?> <span class="ina_countdown"></span></a></p>
		</div>
	</div>
</div>
<?php } ?>

<!--END INACTIVE LOGOUT MODAL CONTENT-->