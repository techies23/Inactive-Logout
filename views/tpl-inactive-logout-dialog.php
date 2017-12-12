<?php
/**
 * Template for Modal.
 *
 * @package inactive-logout
 */

?>

<?php
$override = is_multisite() ? get_site_option( '__ina_overrideby_multisite_setting' ) : false;
$ina_full_overlay         = get_option( '__ina_full_overlay' );
$ina_popup_overlay_color  = get_option( '__ina_popup_overlay_color' );
$ina_warn_message_enabled = get_option( '__ina_warn_message_enabled' );

$bg = isset( $ina_popup_overlay_color ) ? $ina_popup_overlay_color : false;
?>

<!--START INACTIVE LOGOUT MODAL CONTENT-->
<span data-bg="<?php echo esc_attr( $bg ); ?>" class="ina__no_confict_popup_bg" data-bgenabled="<?php echo esc_attr( $ina_full_overlay ); ?>"></span>
<?php if ( absint(1) == $ina_warn_message_enabled ) { ?>
<div id="ina__dp_logout_message_box" class="ina-dp-noflict-modal">
	<div class="ina-dp-noflict-modal-content">
		<div class="ina-dp-noflict-modal-body ina-dp-noflict-wakeup">
			<?php
			if ( ! empty( $override ) ) {
				$message_content               = get_site_option( '__ina_warn_message' );
				$time                          = get_site_option( '__ina_logout_time' );
				$ina_multiuser_timeout_enabled = get_site_option( '__ina_enable_timeout_multiusers' );
				if ( $ina_multiuser_timeout_enabled ) {
					global $current_user;
					$ina_multiuser_settings = get_site_option( '__ina_multiusers_settings' );
					foreach ( $ina_multiuser_settings as $ina_multiuser_setting ) {
						if ( in_array( $ina_multiuser_setting['role'], $current_user->roles, true ) ) {
							$time = $ina_multiuser_setting['timeout'] * 60;
						}
					}
				}
			} else {
				$message_content               = get_option( '__ina_warn_message' );
				$time                          = get_option( '__ina_logout_time' );
				$ina_multiuser_timeout_enabled = get_option( '__ina_enable_timeout_multiusers' );
				if ( $ina_multiuser_timeout_enabled ) {
					global $current_user;
					$ina_multiuser_settings = get_option( '__ina_multiusers_settings' );
					foreach ( $ina_multiuser_settings as $ina_multiuser_setting ) {
						if ( in_array( $ina_multiuser_setting['role'], $current_user->roles, true ) ) {
							$time = $ina_multiuser_setting['timeout'] * 60;
						}
					}
				}
			}

			$ina_helpers      = Inactive_Logout_Helpers::instance();
			$replaced_content = str_replace( '{wakup_timout}', $ina_helpers->ina_convert_to_minutes( $time ), $message_content );
			echo apply_filters( 'the_content', $replaced_content ); // WPCS: XSS ok.
			?>
			<p class="ina-dp-noflict-btn-container"><a class="button button-primary ina_stay_logged_in" href="javascript:void(0);"><?php esc_html_e( 'Continue', 'inactive-logout' ); ?></a></p>
		</div>
	</div>
</div>
<?php } else { ?>
<div id="ina__dp_logout_message_box" class="ina-dp-noflict-modal">
	<div class="ina-dp-noflict-modal-content">
		<div class="ina-modal-header">
			<h3><?php esc_html_e( 'Session Timeout', 'inactive-logout' ); ?></h3>
		</div>
		<div class="ina-dp-noflict-modal-body">
			<?php
			if ( ! empty( $override ) ) {
				$message_content = get_site_option( '__ina_logout_message' );
			} else {
				$message_content = get_option( '__ina_logout_message' );
			}
			?>
			<?php echo apply_filters( 'the_content', $message_content ); // WPCS: XSS ok. ?>
			<p class="ina-dp-noflict-btn-container"><a class="button button-primary ina_stay_logged_in" href="javascript:void(0);"><?php esc_html_e( 'Continue', 'inactive-logout' ); ?> <span class="ina_countdown"></span></a></p>
		</div>
	</div>
</div>
<?php } ?>
<!--END INACTIVE LOGOUT MODAL CONTENT-->
