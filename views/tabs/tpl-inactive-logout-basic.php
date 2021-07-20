<?php
/**
 * Template for Basic settings page.
 *
 * @package inactive-logout
 */

?>
<div class="ina-settings-admin-wrap">
    <form method="post" class="ina-form" action="?page=inactive-logout&tab=ina-basic">
		<?php wp_nonce_field( '_nonce_action_save_timeout_settings', '_save_timeout_settings' ); ?>
        <table class="ina-form-tbl form-table">
            <tbody>
			<?php if ( is_network_admin() ) { ?>
                <tr>
                    <th scope="row"><label for="idle_overrideby_multisite_setting"><?php esc_html_e( 'Override for all sites', 'inactive-logout' ); ?></label></th>
                    <td>
                        <input class="regular-text" name="idle_overrideby_multisite_setting" type="checkbox" id="idle_overrideby_multisite_setting" <?php echo ! empty( $idle_overrideby_multisite_setting ) ? 'checked' : false; ?> value="1">
                        <p class="description"><?php esc_html_e( 'When checked below settings will be effective and used for all sites in the network.', 'inactive-logout' ); ?></p>
                    </td>
                </tr>
			<?php } ?>
            <tr>
                <th scope="row"><label for="idle_timeout"><?php esc_html_e( 'Idle Timeout', 'inactive-logout' ); ?></label></th>
                <td>
                    <input class="regular-text" name="idle_timeout" min="1" type="number" id="idle_timeout" value="<?php echo ! empty( $time ) ? esc_attr( $time / 60 ) : 15; ?>">
                    <i><?php esc_html_e( 'Minute(s)', 'inactive-logout' ); ?></i>
                </td>
            </tr>
            <tr class="ina_hide_message_content">
                <th scope="row"><label for="idle_timeout"><?php esc_html_e( 'Idle Message Content', 'inactive-logout' ); ?></label></th>
                <td>
					<?php
					$settings        = array(
						'media_buttons' => false,
						'teeny'         => true,
						'textarea_rows' => 15,
					);
					$message_content = ina_helpers()->get_option( '__ina_logout_message' );
					$content         = $message_content ? $message_content : '<p>You are being timed-out out due to inactivity. Please choose to stay signed in or to logoff.</p><p>Otherwise, you will be logged off automatically.</p>';
					wp_editor( $content, 'idle_message_text', $settings );
					?>
                    <p class="description"><?php esc_html_e( 'Message to be shown when idle timeout screen shows.', 'inactive-logout' ); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="ina_full_overlay"><?php esc_html_e( 'Popup Background', 'inactive-logout' ); ?></label></th>
                <td>
                    <input name="ina_full_overlay" class="ina_apply_background_color" type="checkbox" <?php echo ! empty( $ina_full_overlay ) ? 'checked' : false; ?> value="1">
                    <p class="description"><?php esc_html_e( 'Choose a background color to hide after logout. Enabling this option will remove tranparency.', 'inactive-logout' ); ?></p>
                </td>
            </tr>
            <tr class="ina_colorpicker_show" <?php echo ! empty( $ina_full_overlay ) && (int) $ina_full_overlay === 1 ? 'style="display:table-row;"' : false; ?>>
                <th scope="row"><label for="ina_color_picker"><?php esc_html_e( 'Popup Background Color', 'inactive-logout' ); ?></label></th>
                <td>
                    <input type="text" name="ina_color_picker" value="<?php echo ( ! empty( $ina_popup_overlay_color ) ) ? esc_attr( $ina_popup_overlay_color ) : ''; ?>" class="ina_color_picker">
                    <p class="description"><?php esc_html_e( 'Choose a popup background color.', 'inactive-logout' ); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="idle_countdown_timeout"><?php esc_html_e( 'Timeout Countdown Period', 'inactive-logout' ); ?></label></th>
                <td>
                    <input name="idle_countdown_timeout" type="number" placeholder="10" id="idle_countdown_timeout" value="<?php echo ( ! empty( $countdown_timeout ) ) ? $countdown_timeout : ''; ?>">
                    <p class="description"><?php esc_html_e( 'Countdown before the actual logout displayed to the user in a popup (in seconds). If you set this to 0, automatically counter will be set to 10 seconds.', 'inactive-logout' ); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="idle_disable_countdown"><?php esc_html_e( 'Disable Timeout Countdown', 'inactive-logout' ); ?></label></th>
                <td>
                    <input name="idle_disable_countdown" type="checkbox" id="idle_disable_countdown" <?php echo ! empty( $countdown_enable ) ? 'checked' : false; ?> value="1">
                    <p class="description"><?php esc_html_e( 'When the timeout popup appears, the user is not logged out instantly. It allows the user the chance to continue, or a logout will occur within 10 seconds. Uncheck this feature to immediately logout the user after the chosen time of inactivity.', 'inactive-logout' ); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="ina_disable_login_screen"><?php esc_html_e( 'Disable Login Popup', 'inactive-logout' ); ?></label></th>
                <td>
                    <input name="ina_disable_login_screen" type="checkbox" id="ina_disable_login_screen" <?php echo ! empty( $ina_disable_login_screen ) ? 'checked' : false; ?> value="1">
                    <p class="description"><?php esc_html_e( 'When checked this will disable login popup and show only text that the user has been logged out i.e after session/user is timed out.', 'inactive-logout' ); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="ina_show_warn_message_only"><?php esc_html_e( 'Show Warn Message Only', 'inactive-logout' ); ?></label></th>
                <td>
                    <input name="ina_show_warn_message_only" type="checkbox" id="ina_show_warn_message_only" <?php echo ! empty( $ina_warn_message_enabled ) ? 'checked' : false; ?> value="1">
                    <p class="description"><?php esc_html_e( 'This will show the warning message without the logout URL, but the user will not be logged out.', 'inactive-logout' ); ?></p>
                    <p class="description ina-warn-info"><strong><?php esc_html_e( 'Please note ! Multi role timeout feature will not work when this setting is enabled. Similarly, idle Message Content will be ignored and replaced with this content.', 'inactive-logout' ); ?></strong></p>
                </td>
            </tr>
            <tr class="show_on_warn_message_enabled" <?php echo ! empty( $ina_warn_message_enabled ) && (int) $ina_warn_message_enabled === 1 ? 'style="display:table-row;"' : 'style="display:none;"'; ?>>
                <th scope="row"><label for="ina_show_warn_message"><?php esc_html_e( 'Warn Message Content', 'inactive-logout' ); ?></label></th>
                <td>
					<?php
					$settings_warn        = array(
						'media_buttons' => false,
						'teeny'         => true,
						'textarea_rows' => 15,
					);
					$__ina_warn_message   = ina_helpers()->get_option( '__ina_warn_message' );
					$content_warn_message = $__ina_warn_message ? $__ina_warn_message : '<h3>Wakeup !</h3><p>You have been inactive for {wakup_timout}. Press continue to continue browsing.</p>';
					wp_editor( $content_warn_message, 'ina_show_warn_message', $settings_warn );
					?>
                    <p class="description"><?php esc_html_e( 'Use {wakup_timout} to show minutes. This is message that will be shown when inactive.', 'inactive-logout' ); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="ina_disable_multiple_login"><?php esc_html_e( 'Disable Concurrent Logins', 'inactive-logout' ); ?></label></th>
                <td>
                    <input name="ina_disable_multiple_login" type="checkbox" id="ina_disable_multiple_login" <?php echo ! empty( $ina_concurrent ) ? 'checked' : false; ?> value="1">
                    <p class="description"><?php esc_html_e( 'This will disable the user from logging in using the same account at different locations.', 'inactive-logout' ); ?></p>
                </td>
            </tr>
			<?php do_action( 'ina__addon_form_elements' ); ?>
            <tr>
                <th scope="row"><label for="ina_enable_redirect_link"><?php esc_html_e( 'Enable Redirect', 'inactive-logout' ); ?></label></th>
                <td>
                    <input name="ina_enable_redirect_link" type="checkbox" <?php echo ! empty( $ina_enable_redirect ) ? 'checked' : false; ?> id="ina_enable_redirect_link" value="1">
                    <p class="description"><?php esc_html_e( 'If not checked then user will be logged out to login screen after timeout.', 'inactive-logout' ); ?></p>
                </td>
            </tr>
            <tr class="show_on_enable_redirect_link" <?php echo ! empty( $ina_enable_redirect ) && (int) $ina_enable_redirect === 1 ? 'style="display:table-row;"' : 'style="display:none;"'; ?>>
                <th scope="row"><label for="ina_redirect_page"><?php esc_html_e( 'Redirect Page', 'inactive-logout' ); ?></label></th>
                <td>
                    <select name="ina_redirect_page" class="ina_redirect_page regular-text ina-hacking-select">
                        <option value="custom-page-redirect"><?php esc_html_e( 'External Page Redirect', 'inactive-logout' ); ?></option>
						<?php
						$posts = ina_helpers()->ina_get_all_pages_posts();
						if ( ! empty( $posts ) && ! empty( $ina_redirect_page_link ) ) {
							foreach ( $posts as $k => $post_types ) {
								?>
                                <optgroup label="<?php echo ucfirst( $k ); ?>">
									<?php foreach ( $post_types as $post_type ) { ?>
                                        <option <?php echo ( intval( $ina_redirect_page_link ) === $post_type['ID'] ) ? esc_attr( 'selected' ) : ''; ?>
                                                value="<?php echo esc_attr( $post_type['ID'] ); ?>">
											<?php echo esc_html( $post_type['title'] ); ?>
                                        </option>
									<?php } ?>
                                </optgroup>
								<?php
							}
						} else {
							?>
                            <option value=""><?php esc_html_e( 'No Posts Found.', 'inactive-logout' ); ?></option>
							<?php
						}
						?>
                    </select>
                    <p class="description"><?php esc_html_e( 'Select a page to redirect to after session timeout and clicking OK.', 'inactive-logout' ); ?></p>
                </td>
            </tr>
            <tr class="show_cutom_redirect_textfield" <?php echo ! empty( $ina_enable_redirect ) ? false : 'style=display:none;'; ?> <?php echo ! empty( $ina_redirect_page_link ) && 'custom-page-redirect' === $ina_redirect_page_link ? false : 'style="display:none;"'; ?> >
                <th scope="row"><label for="custom_redirect_text_field"><?php esc_html_e( 'Custom URL Redirect', 'inactive-logout' ); ?></label></th>
                <td>
                    <input name="custom_redirect_text_field" type="url" id="custom_redirect_text_field" class="regular-text code" value="<?php echo ( ! empty( $custom_redirect_text_field ) ) ? esc_attr( $custom_redirect_text_field ) : false; ?>">
                    <p class="description">
						<?php
						// translators: Url.
						printf( esc_html__( 'Link to custom url redirect. Ex: %s', 'inactive-logout' ), esc_url( 'https://deepenbajracharya.com.np/' ) );
						?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="ina_enable_debugger"><?php esc_html_e( 'Enable Debugger ?', 'inactive-logout' ); ?></label></th>
                <td>
                    <input name="ina_enable_debugger" type="checkbox" <?php echo ! empty( $ina_enable_debugger ) ? 'checked' : false; ?> id="ina_enable_debugger" value="1">
                    <p class="description"><?php esc_html_e( 'Enable debugger window for debugging logout issue. Note: Debugger will not work properly for multi tabs. Countdown timer will be based on last active tab.', 'inactive-logout' ); ?></p>
                </td>
            </tr>
            </tbody>
        </table>
        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_html_e( 'Save Changes', 'inactive-logout' ); ?>"> <span class="description" style="font-style: italic; margin-left:10px; color:red;"><?php esc_html_e( 'Please refresh this page properly after saving in order to reflect changes in the settings.', 'inactive-logout' ); ?></span></p>

    </form>
</div>