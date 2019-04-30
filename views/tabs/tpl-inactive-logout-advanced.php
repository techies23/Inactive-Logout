<?php
/**
 * Template for Advance settings page.
 *
 * @package inactive-logout
 */

?>

<?php $result_roles = $this->helper->ina_get_all_roles(); ?>
<div id="message" class="updated notice is-dismissible" style="display: none;"></div>

<form method="post" class="ina-form" action="?page=inactive-logout&tab=ina-advanced">
	<?php wp_nonce_field( '_nonce_action_save_timeout_adv_settings', '_save_timeout_adv_settings' ); ?>
    <table class="ina-form-tbl form-table">
        <tbody>
        <tr>
            <th scope="row"><label for="ina_enable_different_role_timeout"><?php esc_html_e( 'Multi-Role Timeout', 'inactive-logout' ); ?></label></th>
            <td>
                <input name="ina_enable_different_role_timeout" type="checkbox" id="ina_enable_different_role_timeout" <?php echo ! empty( $ina_multiuser_timeout_enabled ) ? 'checked' : false; ?> value="1">
                <p class="description"><?php esc_html_e( 'This will enable multi-user role timeout functionality.', 'inactive-logout' ); ?></p>
            </td>
        </tr>
        <tr class="ina-multi-role-table" <?php echo ! empty( $ina_multiuser_timeout_enabled ) && (int) $ina_multiuser_timeout_enabled === 1 ? 'style="display:table-row;"' : 'style="display:none;"'; ?>>
            <th scope="row"><label for="idle_timeout"><?php esc_html_e( 'Enable Multi-User Feature', 'inactive-logout' ); ?></label></th>
            <td>
                <select class="ina-hacking-multi-select" id="ina_definetime_specific_userroles" multiple="multiple" name="ina_multiuser_roles[]">
					<?php
					foreach ( $result_roles as $k => $role ) {
						$selected = $this->helper->ina_check_role_enabledfor_multiuser( $k );
						?>
                        <option value="<?php echo esc_attr( $k ); ?>" <?php echo ! empty( $selected ) ? 'selected' : false; ?>><?php echo esc_html( $role ); ?></option>
						<?php
					}
					?>
                </select>
                <p class="description"><i><?php esc_html_e( 'This will allow you to define different timeout constraint according to different selected user roles.', 'inactive-logout' ); ?></i></p>
            </td>
        </tr>
        </tbody>
    </table>
	<?php if ( ! empty( $ina_multiuser_settings ) ) { ?>
        <table class="ina-form-tbl ina-multi-role-table wp-list-table widefat fixed striped pages">
            <thead>
            <th class="manage-column" width="10%"><?php esc_html_e( 'User Role', 'inactive-logout' ); ?></th>
            <th class="manage-column" width="15%"><?php esc_html_e( 'Timeout (In Minutes)', 'inactive-logout' ); ?></th>
            <th class="manage-column" width="40%"><?php esc_html_e( 'Redirect Page', 'inactive-logout' ); ?></th>
            <th class="manage-column" width="10%"><?php esc_html_e( 'Disable', 'inactive-logout' ); ?></th>
            <th class="manage-column" width="20%"><?php esc_html_e( 'Disable Concurrent Login', 'inactive-logout' ); ?></th>
            </thead>
            <tbody>
			<?php
			foreach ( $ina_multiuser_settings as $k => $ina_multiuser_setting ) {
				$role = $ina_multiuser_setting['role'];
				?>
                <tr>
                    <td><?php echo esc_html( $result_roles[ $role ] ); ?></td>
                    <td><input type="number" min="1" value="<?php echo ( ! empty( $ina_multiuser_setting['timeout'] ) ) ? esc_attr( $ina_multiuser_setting['timeout'] ) : 15; ?>" name="ina_individual_user_timeout[]"></td>
                    <td>
                        <select name="ina_redirect_page_individual_user[]" class="regular-text ina-hacking-select">
                            <option value="0"><?php esc_html_e( 'Set Global Redirect Page', 'inactive-logout' ); ?></option>
							<?php
							$ina_helpers = Inactive_Logout_Helpers::instance();
							$posts       = $ina_helpers->ina_get_all_pages_posts();
							if ( $posts ) {
								?>
                                <optgroup label="Posts">
									<?php
									foreach ( $posts as $post ) {
										if ( 'post' === $post['post_type'] ) {
											?>
                                            <option <?php echo ( intval( $ina_multiuser_setting['redirect_page'] ) === $post['ID'] ) ? esc_attr( 'selected' ) : ''; ?>
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
                                            <option <?php echo ( intval( $ina_multiuser_setting['redirect_page'] ) === $post['ID'] ) ? esc_attr( 'selected' ) : ''; ?>
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
                    </td>
                    <td><input type="checkbox" name="ina_disable_inactive_logout[<?php echo esc_attr( $role ); ?>]" <?php echo ( ! empty( $ina_multiuser_setting['disabled_feature'] ) ) ? esc_attr( 'checked' ) : false; ?> value="1"></td>
                    <td><input type="checkbox" name="ina_disable_inactive_concurrent_login[<?php echo esc_attr( $role ); ?>]" <?php echo ( ! empty( $ina_multiuser_setting['disabled_concurrent_login'] ) ) ? esc_attr( 'checked' ) : false; ?> value="1"></td>
                </tr>
			<?php } ?>
            </tbody>
            <tfoot>
            <tr>
                <th colspan="5">
					<?php $bold_string = '<span class="ina-highlight"><strong>"' . esc_html__( 'Disable', 'inactive-logout' ) . '"</strong></span>'; ?>
                    <p class="description ina-warn-info" style="float:right;">
						<?php printf( __( 'Note: %s is used for disabling inactive logout functionality to that specific user.', 'inactive-logout' ), $bold_string ); ?>
                    </p>
                </th>
            </tr>
            </tfoot>
        </table>
	<?php } ?>
    <p class="ina_adv_submit"><input type="submit" name="adv_submit" id="submit" class="button button-primary" value="<?php esc_html_e( 'Save Changes', 'inactive-logout' ); ?>"> <a id="ina-reset-adv-data" class="button button-primary button-reset-ina" data-msg="<?php esc_html_e( 'Are you sure you want to erase all advanced settings. This cannot be undone !', 'inactive-logout' ); ?>"><?php esc_html_e( 'Reset Advanced Settings !', 'inactive-logout' ); ?></a></p>
</form>
