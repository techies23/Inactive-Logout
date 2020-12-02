<?php
/**
 * File contains functions for Logout.
 *
 * @package inactive-logout
 */

// Not Permission to agree more or less then given.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Core Functions
 *
 * @since   1.0.0
 * @author  Deepen
 */
class Inactive_Logout_Functions {

	/**
	 * Inactive_Logout_Functions constructor.
	 */
	public function __construct() {
		$this->loadHooks();
	}

	/**
	 * Loading Hooks
	 */
	public function loadHooks() {
		if ( is_user_logged_in() ) {
			add_action( 'wp_footer', array( $this, 'dialog_modal' ) );
			add_action( 'admin_footer', array( $this, 'dialog_modal' ) );

			//Acutually Logging out here
			add_action( 'wp_ajax_ina_logout_session', array( $this, 'logout_this_session' ) );

			// Ajax for resetting.
			add_action( 'wp_ajax_ina_reset_adv_settings', array( $this, 'ina_reset_adv_settings' ) );

			// Ajax for User Roles only.
			add_action( 'wp_ajax_ina_save_disabled_roles', array( $this, 'ina_save_disabled_roles' ) );
			add_action( 'wp_ajax_ina_get_enabled_roles', array( $this, 'ina_get_enabled_roles' ) );

			//Mailpoet conflict resolve whitelist
			add_filter( 'mailpoet_conflict_resolver_whitelist_style', array( $this, 'conflict_resolver' ) );
		}

		// Ajax for checking last session.
		add_action( 'wp_ajax_ina_checklastSession', array( $this, 'last_session' ) );
		add_action( 'wp_ajax_nopriv_ina_checklastSession', array( $this, 'last_session' ) );
	}

	/**
	 * Logout Sessions
	 */
	public function logout_this_session() {
		//Logout Now
		wp_logout();

		$message = apply_filters( 'ina__logout_message', esc_html__( 'You have been logged out because of inactivity.', 'inactive-logout' ) );
		wp_send_json( array(
			'msg' => $message
		) );
		wp_die();
	}

	/**
	 * Check Last Session and Logout User
	 */
	public function last_session() {
		if ( ! is_user_logged_in() ) {
			wp_send_json_error( __( 'Session is already logged out.', 'inactive-logout' ) );
		} else {
			$timestamp = filter_input( INPUT_POST, 'timestamp' );
			$timestamp = ( isset( $timestamp ) ) ? $timestamp : null;

			$do = filter_input( INPUT_POST, 'do', FILTER_SANITIZE_STRING );
			switch ( $do ) {
				case 'ina_updateLastSession':
					update_user_meta( get_current_user_id(), '__ina_last_active_session', $timestamp );

					$html = $this->trigger_logout_dialog();
					wp_send_json( array(
						'html' => $html
					) );
					break;
				case 'ina_logout':
					$override = is_multisite() ? get_site_option( '__ina_overrideby_multisite_setting' ) : false;
					// Check in case of Multisite Active.
					if ( ! empty( $override ) ) {
						$ina_enable_redirect    = get_site_option( '__ina_enable_redirect' );
						$ina_redirect_page_link = get_site_option( '__ina_redirect_page_link' );
						// Enabled Multi user Timeout.
						$ina_multiuser_timeout_enabled = get_site_option( '__ina_enable_timeout_multiusers' );

						if ( ! empty( $ina_enable_redirect ) ) {
							if ( 'custom-page-redirect' === $ina_redirect_page_link ) {
								$ina_redirect_page_link = get_site_option( '__ina_custom_redirect_text_field' );
								$redirect_link          = $ina_redirect_page_link;
							} else {
								$redirect_link = get_the_permalink( $ina_redirect_page_link );
							}
						}

						if ( $ina_multiuser_timeout_enabled ) {
							global $current_user;
							$ina_multiuser_settings = get_site_option( '__ina_multiusers_settings' );
							foreach ( $ina_multiuser_settings as $ina_multiuser_setting ) {
								if ( in_array( $ina_multiuser_setting['role'], $current_user->roles, true ) ) {
									$redirect_link = get_the_permalink( $ina_multiuser_setting['redirect_page'] );
								}
							}
						}
					} else {
						$ina_enable_redirect    = get_option( '__ina_enable_redirect' );
						$ina_redirect_page_link = get_option( '__ina_redirect_page_link' );
						// Enabled Multi user Timeout.
						$ina_multiuser_timeout_enabled = get_option( '__ina_enable_timeout_multiusers' );

						if ( ! empty( $ina_enable_redirect ) ) {
							if ( 'custom-page-redirect' === $ina_redirect_page_link ) {
								$ina_redirect_page_link = get_option( '__ina_custom_redirect_text_field' );
								$redirect_link          = $ina_redirect_page_link;
							} else {
								$redirect_link = get_the_permalink( $ina_redirect_page_link );
							}
						}

						if ( $ina_multiuser_timeout_enabled ) {
							global $current_user;
							$ina_multiuser_settings = get_option( '__ina_multiusers_settings' );
							foreach ( $ina_multiuser_settings as $ina_multiuser_setting ) {
								if ( in_array( $ina_multiuser_setting['role'], $current_user->roles, true ) ) {
									$redirect_link = get_the_permalink( $ina_multiuser_setting['redirect_page'] );
								}
							}
						}
					}

					// Logout Current Users.
					if ( ! empty( $redirect_link ) ) {
						$message = apply_filters( 'ina__redirect_message', esc_html__( 'You have been logged out because of inactivity. Please wait while we redirect you to a certain page...', 'inactive-logout' ) );
					} else {
						$message = apply_filters( 'ina__logout_message', esc_html__( 'You have been logged out because of inactivity.', 'inactive-logout' ) );
					}

					wp_send_json( array(
						'msg'          => $message,
						'redirect_url' => isset( $redirect_link ) ? $redirect_link : false,
					) );
					break;

				default:
					break;
			}
		}

		wp_die();
	}

	/**
	 * OUTPUT HTML for Dialog
	 *
	 * @return false|string
	 */
	public function trigger_logout_dialog() {
		ob_start();
		$override                 = is_multisite() ? get_site_option( '__ina_overrideby_multisite_setting' ) : false;
		$ina_warn_message_enabled = get_option( '__ina_warn_message_enabled' );
		?>
        <!--START INACTIVE LOGOUT MODAL CONTENT-->
		<?php if ( absint( 1 ) == $ina_warn_message_enabled ) { ?>
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

					$replaced_content = str_replace( '{wakup_timout}', ina_helpers()->ina_convert_to_minutes( $time ), $message_content );

					if ( function_exists( 'icl_register_string' ) ) {
						icl_register_string( 'inactive-logout', 'inactive_logout_dynamic_wakeup_text', esc_html( $replaced_content ) );
						echo wpautop( icl_t( 'inactive-logout', 'inactive_logout_dynamic_wakeup_text', $replaced_content ) );
					} else {
						echo wpautop( $replaced_content );
					}
					?>
                    <p class="ina-dp-noflict-btn-container"><a class="button button-primary ina_stay_logged_in" href="javascript:void(0);"><?php esc_html_e( 'Continue', 'inactive-logout' ); ?></a></p>
                </div>
            </div>
		<?php } else { ?>
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

					if ( function_exists( 'icl_register_string' ) ) {
						icl_register_string( 'inactive-logout', 'inactive_logout_dynamic_popup_text', esc_html( $message_content ) );
						echo wpautop( icl_t( 'inactive-logout', 'inactive_logout_dynamic_popup_text', $message_content ) );
					} else {
						echo wpautop( $message_content );
					}
					?>
                    <p class="ina-dp-noflict-btn-container"><a class="button button-primary ina_stay_logged_in" href="javascript:void(0);"><?php esc_html_e( 'Continue', 'inactive-logout' ); ?> <span class="ina_countdown"></span></a></p>
                </div>
            </div>
            <!--END INACTIVE LOGOUT MODAL CONTENT-->
			<?php
		}

		return ob_get_clean();
	}

	/**
	 * Reset Advanced Settings
	 *
	 * @since  1.3.0
	 */
	public function ina_reset_adv_settings() {
		check_ajax_referer( '_ina_nonce_security', 'security' );
		delete_option( '__ina_roles' );
		delete_option( '__ina_enable_timeout_multiusers' );
		delete_option( '__ina_multiusers_settings' );

		if ( is_network_admin() && is_multisite() ) {
			delete_site_option( '__ina_roles' );
			delete_site_option( '__ina_enable_timeout_multiusers' );
			delete_site_option( '__ina_multiusers_settings' );
		}

		wp_send_json( array(
			'code' => 1,
			'msg'  => esc_html__( 'Reset advanced settings successful.', 'inactive-logout' ),
		) );
		wp_die();
	}

	/**
	 * Adding Dialog in footer
	 */
	public function dialog_modal() {
		$disable_timeoutjs = ina_helpers()->ina_check_user_role();
		if ( ! $disable_timeoutjs ) {
			$ina_full_overlay        = get_option( '__ina_full_overlay' );
			$ina_popup_overlay_color = get_option( '__ina_popup_overlay_color' );
			$bg                      = false;
			if ( ! empty( $ina_full_overlay ) ) {
				$bg = isset( $ina_popup_overlay_color ) ? 'style="background-color:' . $ina_popup_overlay_color . '"' : '#000000';
			}
			?>
            <!--START INACTIVE LOGOUT MODAL CONTENT-->
            <div id="ina__dp_logout_message_box" class="ina-dp-noflict-modal" <?php echo ! empty( $bg ) ? $bg : ''; ?>></div>
            <!--END INACTIVE LOGOUT MODAL CONTENT-->
			<?php
		}
	}

	/**
	 * Whitelist inactive logout script in mailpoet pages.
	 *
	 * @TODO TESTING FROM THE DARK SIDE.
	 *
	 * @param $list
	 *
	 * @return array
	 */
	public function conflict_resolver( $list ) {
		$turnoff = apply_filters( 'ina_logout_mailpoet_conflict_fix', true );
		if ( $turnoff && ! in_array( 'inactive-logout', $list ) ) {
			$list[] = 'inactive-logout';
		}

		return $list;
	}
}

new Inactive_Logout_Functions();
