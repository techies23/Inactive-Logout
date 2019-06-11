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
                    <input class="regular-text" name="idle_timeout" min="1" type="number" id="idle_timeout" value="<?php echo ( isset( $time ) ) ? esc_attr( $time / 60 ) : 30; ?>">
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
					$message_content = get_option( '__ina_logout_message' );
					$content         = $message_content ? $message_content : null;
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
                <th scope="row"><label for="idle_disable_countdown"><?php esc_html_e( 'Disable Timeout Countdown', 'inactive-logout' ); ?></label></th>
                <td>
                    <input name="idle_disable_countdown" type="checkbox" id="idle_disable_countdown" <?php echo ! empty( $countdown_enable ) ? 'checked' : false; ?> value="1">
                    <p class="description"><?php esc_html_e( 'When the timeout popup appears, the user is not logged out instantly. It allows the user the chance to continue, or a logout will occur within 10 seconds. Uncheck this feature to immediately logout the user after the chosen time of inactivity.', 'inactive-logout' ); ?></p>
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
					$__ina_warn_message   = get_option( '__ina_warn_message' );
					$content_warn_message = $__ina_warn_message ? $__ina_warn_message : null;
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
                    <select name="ina_redirect_page" class="ina_redirect_page regular-text">
                        <option value="custom-page-redirect"><?php esc_html_e( 'External Page Redirect', 'inactive-logout' ); ?></option>
						<?php
						$posts = ina_helpers()->ina_get_all_pages_posts();
						if ( ! empty( $posts ) ) {
							?>
                            <optgroup label="Posts">
								<?php
								foreach ( $posts as $post ) {
									if ( 'post' === $post['post_type'] ) {
										?>
                                        <option <?php echo ( intval( $ina_redirect_page_link ) === $post['ID'] ) ? esc_attr( 'selected' ) : ''; ?>
                                                value="<?php echo esc_attr( $post['ID'] ); ?>">
											<?php echo esc_html( $post['title'] ); ?>
                                        </option>
										<?php
									}
								}
								?>
                            </optgroup>
                            <optgroup label="Pages">
								<?php
								foreach ( $posts as $post ) {
									if ( 'page' === $post['post_type'] ) {
										?>
                                        <option <?php echo ( intval( $ina_redirect_page_link ) === $post['ID'] ) ? esc_attr( 'selected' ) : ''; ?>
                                                value="<?php echo esc_attr( $post['ID'] ); ?>">
											<?php echo esc_html( $post['title'] ); ?>
                                        </option>
										<?php
									}
								}
								?>
                            </optgroup>
							<?php
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
            </tbody>
        </table>
        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_html_e( 'Save Changes', 'inactive-logout' ); ?>"></p>
    </form>

	<?php ina_helpers()->show_plugin_like(); ?>
</div>