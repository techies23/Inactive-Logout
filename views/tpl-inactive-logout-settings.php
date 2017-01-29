<div class="wrap">
	<h1><?php _e("Inactive User Logout Settings", "ina-logout"); ?></h1>
	<?php if( $saved ) { ?>
	<div id="message" class="updated notice is-dismissible"><p><?php _e("Updated !", "ina-logout"); ?></p><button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php _e("Dismiss this notice.", "ina-logout"); ?></span></button></div>
	<?php } ?>
	<form method="post" action="?page=inactive-logout&tab=inactive-logout-basic">
		<?php wp_nonce_field( '_nonce_action_save_timeout_settings', '_save_timeout_settings' ); ?>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row"><label for="idle_timeout"><?php _e("Idle Timeout", "ina-logout"); ?></label></th>
					<td>
						<input name="idle_timeout" type="number" id="idle_timeout" value="<?php echo isset($time) ? $time/60 : 30; ?>" >
						<i><?php _e("Minute(s)", "ina-logout"); ?></i>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="idle_timeout"><?php _e("Idle Message Content", "ina-logout"); ?></label></th>
					<td>
						<?php 
						$settings = array( 
							'media_buttons' => false,
							'teeny' => true,
							'textarea_rows' => 15
							);
						$message_content = get_option( '__ina_logout_message' );
						$content = $message_content ? $message_content : NULL;
						wp_editor( $content, 'idle_message_text', $settings );
						?>
						<p class="description"><?php _e("Message to be shown when idle timeout screen shows.", "ina-logout"); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="ina_full_overlay"><?php _e("Popup Background", "ina-logout"); ?></label></th>
					<td>
						<input name="ina_full_overlay" type="checkbox" <?php echo !empty($ina_full_overlay) ? "checked" : false; ?> value="1" >
						<p class="description"><?php _e("Choose a background color to hide after logout. Enabling this option will remove tranparency.", "ina-logout"); ?></p>
					</td>
				</tr>
				<tr class="ina_colorpicker_show">
					<th scope="row"><label for="ina_color_picker"><?php _e("Popup Background Color", "ina-logout"); ?></label></th>
					<td>
						<input type="text" name="ina_color_picker" value="<?php echo !empty($ina_popup_overlay_color) ? $ina_popup_overlay_color : ""; ?>" class="ina_color_picker" >
						<p class="description"><?php _e("Choose a popup background color.", "ina-logout"); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="idle_disable_countdown"><?php _e("Disable Timeout Countdown", "ina-logout"); ?></label></th>
					<td>
						<input name="idle_disable_countdown" type="checkbox" id="idle_disable_countdown" <?php echo !empty($countdown_enable) ? "checked" : false; ?> value="1" >
						<p class="description"><?php _e("When timeout popup is shown user is not logged out instantly. It gives user a chance to keep using or logout for 10 seconds. Remove this feature and directly log out after inactive.", "ina-logout"); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="ina_show_warn_message_only"><?php _e("Show Warn Message Only", "ina-logout"); ?></label></th>
					<td>
						<input name="ina_show_warn_message_only" type="checkbox" id="ina_show_warn_message_only" <?php echo !empty($ina_warn_message_enabled) ? "checked" : false; ?> value="1" >
						<p class="description"><?php _e("Will show warn message without logout url but user will not log out.", "ina-logout"); ?></p>
					</td>
				</tr>
				<tr class="show_on_warn_message_enabled">
					<th scope="row"><label for="ina_show_warn_message"><?php _e("Warn Message Content", "ina-logout"); ?></label></th>
					<td>
						<?php 
						$settings_warn = array( 
							'media_buttons' => false,
							'teeny' => true,
							'textarea_rows' => 15
							);
						$__ina_warn_message = get_option( '__ina_warn_message' );
						$content_warn_message = $__ina_warn_message ? $__ina_warn_message : NULL;
						wp_editor( $content_warn_message, 'ina_show_warn_message', $settings_warn );
						?>
						<p class="description"><?php _e("Use {wakup_timout} to show minutes. This is message that will be shown when inactive.", "ina-logout"); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="ina_disable_multiple_login"><?php _e("Disable Concurrent Logins", "ina-logout"); ?></label></th>
					<td>
						<input name="ina_disable_multiple_login" type="checkbox" id="ina_disable_multiple_login" <?php echo !empty($ina_concurrent) ? "checked" : false; ?> value="1" >
						<p class="description"><?php _e("This will unable user to login using same account in different places.", "ina-logout"); ?></p>
					</td>
				</tr>
			</tbody>
		</table>
		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e("Save Changes", "ina-logout"); ?>"></p>
	</form>
</div>