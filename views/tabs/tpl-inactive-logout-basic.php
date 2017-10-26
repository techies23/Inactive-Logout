<form method="post" action="?page=inactive-logout&tab=ina-basic">
  <?php wp_nonce_field( '_nonce_action_save_timeout_settings', '_save_timeout_settings' ); ?>
  <table class="ina-form-tbl form-table">
	<tbody>
		<?php if ( is_network_admin() ) { ?>
	  <tr>
		<th scope="row"><label for="idle_overrideby_multisite_setting"><?php _e( 'Override for all sites', 'inactive-logout' ); ?></label></th>
		<td>
		  <input name="idle_overrideby_multisite_setting" type="checkbox" id="idle_overrideby_multisite_setting" <?php echo ! empty( $idle_overrideby_multisite_setting ) ? 'checked' : false; ?> value="1" >
		  <p class="description"><?php _e( 'When checked below settings will be effective and used for all sites in the network.', 'inactive-logout' ); ?></p>
		</td>
	  </tr>
		<?php } ?>
	  <tr>
		<th scope="row"><label for="idle_timeout"><?php _e( 'Idle Timeout', 'inactive-logout' ); ?></label></th>
		<td>
		  <input name="idle_timeout" min="1" type="number" id="idle_timeout" value="<?php echo isset( $time ) ? $time / 60 : 30; ?>" >
		  <i><?php _e( 'Minute(s)', 'inactive-logout' ); ?></i>
		</td>
	  </tr>
	  <tr class="ina_hide_message_content">
		<th scope="row"><label for="idle_timeout"><?php _e( 'Idle Message Content', 'inactive-logout' ); ?></label></th>
		<td>
			<?php
			$settings = array(
				'media_buttons' => false,
				'teeny' => true,
				'textarea_rows' => 15,
			);
			$message_content = get_option( '__ina_logout_message' );
			$content = $message_content ? $message_content : null;
			wp_editor( $content, 'idle_message_text', $settings );
			?>
		  <p class="description"><?php _e( 'Message to be shown when idle timeout screen shows.', 'inactive-logout' ); ?></p>
		</td>
	  </tr>
	  <tr>
		<th scope="row"><label for="ina_full_overlay"><?php _e( 'Popup Background', 'inactive-logout' ); ?></label></th>
		<td>
		  <input name="ina_full_overlay" type="checkbox" <?php echo ! empty( $ina_full_overlay ) ? 'checked' : false; ?> value="1" >
		  <p class="description"><?php _e( 'Choose a background color to hide after logout. Enabling this option will remove tranparency.', 'inactive-logout' ); ?></p>
		</td>
	  </tr>
	  <tr class="ina_colorpicker_show">
		<th scope="row"><label for="ina_color_picker"><?php _e( 'Popup Background Color', 'inactive-logout' ); ?></label></th>
		<td>
		  <input type="text" name="ina_color_picker" value="<?php echo ! empty( $ina_popup_overlay_color ) ? $ina_popup_overlay_color : ''; ?>" class="ina_color_picker" >
		  <p class="description"><?php _e( 'Choose a popup background color.', 'inactive-logout' ); ?></p>
		</td>
	  </tr>
	  <tr>
		<th scope="row"><label for="idle_disable_countdown"><?php _e( 'Disable Timeout Countdown', 'inactive-logout' ); ?></label></th>
		<td>
		  <input name="idle_disable_countdown" type="checkbox" id="idle_disable_countdown" <?php echo ! empty( $countdown_enable ) ? 'checked' : false; ?> value="1" >
		  <p class="description"><?php _e( 'When timeout popup is shown user is not logged out instantly. It gives user a chance to keep using or logout for 10 seconds. Remove this feature and directly log out after inactive.', 'inactive-logout' ); ?></p>
		</td>
	  </tr>
	  <tr>
		<th scope="row"><label for="ina_show_warn_message_only"><?php _e( 'Show Warn Message Only', 'inactive-logout' ); ?></label></th>
		<td>
		  <input name="ina_show_warn_message_only" type="checkbox" id="ina_show_warn_message_only" <?php echo ! empty( $ina_warn_message_enabled ) ? 'checked' : false; ?> value="1" >
		  <p class="description"><?php _e( 'Will show warn message without logout url but user will not log out.', 'inactive-logout' ); ?></p>
		  <p class="description ina-warn-info"><strong><?php _e( 'Please note ! Multi role timeout feature will not work when this setting is enabled. Similarly, idle Message Content will be ignored and replaced with this content.', 'inactive-logout' ); ?></strong></p>
		</td>
	  </tr>
	  <tr class="show_on_warn_message_enabled">
		<th scope="row"><label for="ina_show_warn_message"><?php _e( 'Warn Message Content', 'inactive-logout' ); ?></label></th>
		<td>
			<?php
			$settings_warn = array(
				'media_buttons' => false,
				'teeny' => true,
				'textarea_rows' => 15,
			);
			$__ina_warn_message = get_option( '__ina_warn_message' );
			$content_warn_message = $__ina_warn_message ? $__ina_warn_message : null;
			wp_editor( $content_warn_message, 'ina_show_warn_message', $settings_warn );
			?>
		  <p class="description"><?php _e( 'Use {wakup_timout} to show minutes. This is message that will be shown when inactive.', 'inactive-logout' ); ?></p>
		</td>
	  </tr>
	  <tr>
		<th scope="row"><label for="ina_disable_multiple_login"><?php _e( 'Disable Concurrent Logins', 'inactive-logout' ); ?></label></th>
		<td>
		  <input name="ina_disable_multiple_login" type="checkbox" id="ina_disable_multiple_login" <?php echo ! empty( $ina_concurrent ) ? 'checked' : false; ?> value="1" >
		  <p class="description"><?php _e( 'This will unable user to login using same account in different places.', 'inactive-logout' ); ?></p>
		</td>
	  </tr>
	  <tr>
		<th scope="row"><label for="ina_enable_redirect_link"><?php _e( 'Enable Redirect', 'inactive-logout' ); ?></label></th>
		<td>
		  <input name="ina_enable_redirect_link" type="checkbox" <?php echo ! empty( $ina_enable_redirect ) ? 'checked' : false; ?> id="ina_enable_redirect_link" value="1" >
		  <p class="description"><?php _e( 'If not checked then user will be logged out to login screen after timeout.', 'inactive-logout' ); ?></p>
		</td>
	  </tr>
	  <tr class="show_on_enable_redirect_link" style="display:none;">
		<th scope="row"><label for="ina_redirect_page"><?php _e( 'Redirect Page', 'inactive-logout' ); ?></label></th>
		<td>
		  <select name="ina_redirect_page" class="regular-text ina-hacking-select">
			<option value="custom-page-redirect"><?php _e( 'External Page Redirect', 'inactive-logout' ); ?></option>
			<?php
			$posts = Inactive_Logout_Functions::ina_get_all_pages_posts();
			if ( $posts ) {
			?>
			<optgroup label="Posts">
				<?php
				foreach ( $posts as $post ) {
					if ( $post['post_type'] == 'post' ) {
					?>
					<option <?php echo $ina_redirect_page_link == $post['ID'] ? 'selected' : null; ?> value="<?php echo $post['ID']; ?>"><?php echo $post['title']; ?></option>
					<?php
					}
				}
				?>
			</optgroup>
			<optgroup label="Pages">
				<?php
				foreach ( $posts as $post ) {
					if ( $post['post_type'] == 'page' ) {
					?>
					<option <?php echo $ina_redirect_page_link == $post['ID'] ? 'selected' : null; ?> value="<?php echo $post['ID']; ?>"><?php echo $post['title']; ?></option>
					<?php
					}
				}
				?>
			</optgroup>
			<?php
			} else {
				?>
			  <option value=""><?php _e( 'No Posts Found.', 'inactive-logout' ); ?></option>
			<?php
			}
			?>
		</select>
		<p class="description"><?php _e( 'Select a page to redirect to after session timeout and clicking OK.', 'inactive-logout' ); ?></p>
	  </td>
	</tr>
	<tr class="show_cutom_redirect_textfield" <?php echo ( ! empty( $ina_redirect_page_link ) && $ina_redirect_page_link == 'custom-page-redirect') ? false : 'style=display:none;'; ?> >
	  <th scope="row"><label for="custom_redirect_text_field"><?php _e( 'Custom URL Redirect', 'inactive-logout' ); ?></label></th>
	  <td>
		<input name="custom_redirect_text_field" type="url" id="custom_redirect_text_field" class="regular-text code" value="<?php echo ! empty( $custom_redirect_text_field ) ? $custom_redirect_text_field : false; ?>">
		<p class="description"><?php printf( __( 'Link to custom url redirect. Ex: %s', 'inactive-logout' ), 'https://deepenbajracharya.com.np/' ); ?></p>
	  </td>
	</tr>
  </tbody>
</table>
<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( 'Save Changes', 'inactive-logout' ); ?>"></p>
</form>

