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

		add_filter( 'auth_cookie_expiration', [ $this, 'auth_expiration' ], 10, 3 );
		add_action( 'wp_ajax_nopriv_ina_ajaxlogin', array( $this, 'login' ) );
		$ina_disable_login_screen = ina_helpers()->get_overrided_option( '__ina_disable_login_screen' );
		if ( empty( $ina_disable_login_screen ) ) {
			add_filter( 'ina__logout_message', [ $this, 'display_login_form' ], 10 );
		}
	}

	/**
	 * Set Default WordPress authencation Cookie Time
	 *
	 * @param $expiration
	 * @param $user_id
	 * @param $remember
	 *
	 * @return int
	 */
	public function auth_expiration( $expiration, $user_id, $remember ) {
		if ( ! $remember ) {
			$expiration = apply_filters( 'ina_change_login_exp_time', 2592000 ); //30 days
		}

		return $expiration;
	}

	/**
	 * Remove this filter in order to just display the message.
	 *
	 * @since 2.0.0
	 */
	public function display_login_form() {
		ob_start();
		?>
        <p><?php esc_html_e( 'You have been logged out because of inactivity. Please login again', 'inactive-logout' ); ?>:</p>
        <div class="ina-loginform-wrapper">
            <span class="ina-login-status"></span>
			<?php do_action( 'ina_before_login_form' ); ?>
            <form id="ina-ajaxlogin-form" class="ina-ajaxlogin-form" method="post" autocomplete="off">
                <div class="content">
                    <div class="input-field">
                        <input id="ina-username" type="text" name="username" required placeholder="<?php esc_attr_e( 'Username', 'inactive-logout' ); ?>">
                    </div>
                    <div class="input-field">
                        <input type="password" placeholder="<?php esc_attr_e( 'Password', 'inactive-logout' ); ?>" name="password" required id="ina-password" autocomplete="off">
                    </div>
                    <a class="lost-password-link" href="<?php echo wp_lostpassword_url(); ?>"><?php esc_html_e( 'Forgot Your Password ?', 'inactive-logout' ); ?></a>
                </div>
                <div class="action">
                    <input class="submit_button" type="submit" value="<?php esc_attr_e( 'Login', 'inactive-logout' ); ?>" name="submit">
                    <a href="javascript:void(0);" onclick="window.location.reload();"><?php esc_html_e( 'Cancel', 'inactive-logout' ); ?></a>
                </div>
            </form>
			<?php do_action( 'ina_after_login_form' ); ?>
        </div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Logout Sessions
	 */
	public function logout_this_session() {
		//Logout Now
		wp_logout();

		$message = apply_filters( 'ina__logout_message', esc_html__( 'You have been logged out because of inactivity.', 'inactive-logout' ) );
		wp_send_json( array(
			'msg'          => $message,
			'nonce'        => wp_create_nonce( '_inaajaxlogin' ),
			'is_logged_in' => is_user_logged_in() ? true : false,
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
					$ina_enable_redirect    = ina_helpers()->get_overrided_option( '__ina_enable_redirect' );
					$ina_redirect_page_link = ina_helpers()->get_overrided_option( '__ina_redirect_page_link' );
					// Enabled Multi user Timeout.
					$ina_multiuser_timeout_enabled = ina_helpers()->get_overrided_option( '__ina_enable_timeout_multiusers' );

					if ( ! empty( $ina_enable_redirect ) ) {
						if ( 'custom-page-redirect' == $ina_redirect_page_link ) {
							$ina_redirect_page_link = ina_helpers()->get_overrided_option( '__ina_custom_redirect_text_field' );
							$redirect_link          = $ina_redirect_page_link;
						} else {
							$redirect_link = get_the_permalink( $ina_redirect_page_link );
						}
					}

					if ( $ina_multiuser_timeout_enabled ) {
						global $current_user;
						$ina_multiuser_settings = ina_helpers()->get_overrided_option( '__ina_multiusers_settings' );
						foreach ( $ina_multiuser_settings as $ina_multiuser_setting ) {
							if ( in_array( $ina_multiuser_setting['role'], $current_user->roles, true ) ) {
								$redirect_link = get_the_permalink( $ina_multiuser_setting['redirect_page'] );
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
		$ina_warn_message_enabled = ina_helpers()->get_overrided_option( '__ina_warn_message_enabled' );
		?>
        <!--START INACTIVE LOGOUT MODAL CONTENT-->
		<?php if ( 1 == $ina_warn_message_enabled ) { ?>
            <div class="ina-dp-noflict-modal-content">
                <div class="ina-dp-noflict-modal-body ina-dp-noflict-wakeup">
					<?php
					$message_content               = ina_helpers()->get_overrided_option( '__ina_warn_message' );
					$time                          = ina_helpers()->get_overrided_option( '__ina_logout_time' );
					$ina_multiuser_timeout_enabled = ina_helpers()->get_overrided_option( '__ina_enable_timeout_multiusers' );
					if ( $ina_multiuser_timeout_enabled ) {
						global $current_user;
						$ina_multiuser_settings = ina_helpers()->get_overrided_option( '__ina_multiusers_settings' );
						foreach ( $ina_multiuser_settings as $ina_multiuser_setting ) {
							if ( in_array( $ina_multiuser_setting['role'], $current_user->roles, true ) ) {
								$time = $ina_multiuser_setting['timeout'] * 60;
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
					$message_content = ina_helpers()->get_overrided_option( '__ina_logout_message' );
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
			$ina_full_overlay        = ina_helpers()->get_overrided_option( '__ina_full_overlay' );
			$ina_popup_overlay_color = ina_helpers()->get_overrided_option( '__ina_popup_overlay_color' );

			$bg = false;
			if ( ! empty( $ina_full_overlay ) ) {
				$bg = isset( $ina_popup_overlay_color ) ? 'style="background-color:' . $ina_popup_overlay_color . '"' : '#000000';
			}
			?>
            <!--START INACTIVE LOGOUT MODAL CONTENT-->
            <div id="ina__dp_logout_message_box" class="ina-dp-noflict-modal" <?php echo ! empty( $bg ) ? $bg : ''; ?>></div>
            <!--END INACTIVE LOGOUT MODAL CONTENT-->
			<?php
		}

		//Debug Bar
		$ina_enable_debugger = ina_helpers()->get_option( '__ina_enable_debugger' );
		if ( $ina_enable_debugger ) {
			require INACTIVE_LOGOUT_VIEWS . '/tpl-debugger.php';
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

	/**
	 * Try login in again if thats what the user wants
	 *
	 * This is for frontend, does not tigger for backend.
	 *
	 * @since 2.0.0
	 * @author MuhammadShabbarAbbas
	 * @modified Deepen on July 2nd, 2021
	 */
	function login() {
		// By default, check_ajax_referer dies if nonce can not been verified
		if ( check_ajax_referer( '_inaajaxlogin', 'nonce', false ) || true ) {
			$info                  = array();
			$info['user_login']    = filter_input( INPUT_POST, 'username' );
			$info['user_password'] = filter_input( INPUT_POST, 'password' );
			$info['remember']      = true;

			$user_signon = wp_signon( $info, false );
			if ( ! is_wp_error( $user_signon ) ) {
				wp_set_current_user( $user_signon->ID );
				wp_set_auth_cookie( $user_signon->ID );
				wp_send_json_success( array( 'message' => '* ' . __( 'Login successful', 'inactive-logout' ) ) );
			} else {
				#wp_send_json_error( array( 'message' => '* ' . $user_signon->get_error_message() ) ); //Disabled because this shows error which allows hacker to know which field is exactly invalidated.
				wp_send_json_error( array( 'message' => '* Invalid username or password.' ) );
			}
		} else {
			wp_send_json_error( array( 'message' => '* ' . __( 'Oh no ! Please refresh your browser and try logging again.', 'inactive-logout' ) ) );
		}

		wp_die();
	}
}

new Inactive_Logout_Functions();
