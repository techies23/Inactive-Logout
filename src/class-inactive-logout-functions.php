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
		add_action( 'wp_footer', array( $this, 'dialog_modal' ) );
		add_action( 'admin_footer', array( $this, 'dialog_modal' ) );

		// Ajax for checking last session.
		add_action( 'wp_ajax_ina_checklastSession', array( $this, 'last_session' ) );
		add_action( 'wp_ajax_nopriv_ina_checklastSession', array( $this, 'last_session' ) );

		//Acutually Logging out here
		add_action( 'wp_ajax_ina_logout_session', array( $this, 'logout_this_session' ) );
		add_action( 'wp_ajax_nopriv_ina_logout_session', array( $this, 'logout_this_session' ) );

		// Ajax for resetting.
		add_action( 'wp_ajax_ina_reset_adv_settings', array( $this, 'ina_reset_adv_settings' ) );

		// Ajax for User Roles only.
		add_action( 'wp_ajax_ina_save_disabled_roles', array( $this, 'ina_save_disabled_roles' ) );
		add_action( 'wp_ajax_ina_get_enabled_roles', array( $this, 'ina_get_enabled_roles' ) );
	}

	public function logout_this_session() {
		check_ajax_referer( '_checklastSession', 'security' );

		//Logout Now
		wp_logout();
		wp_die();
	}

	/**
	 * Check Last Session and Logout User
	 */
	public function last_session() {
		check_ajax_referer( '_checklastSession', 'security' );

		$timestamp = filter_input( INPUT_POST, 'timestamp', FILTER_SANITIZE_STRING );
		$timestamp = ( isset( $timestamp ) ) ? $timestamp : null;

		$do = filter_input( INPUT_POST, 'do', FILTER_SANITIZE_STRING );

		if ( is_user_logged_in() ) {
			switch ( $do ) {
				case 'ina_updateLastSession':
					update_user_meta( get_current_user_id(), '__ina_last_active_session', $timestamp );
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

					wp_send_json(
						array(
							'msg'          => $message,
							'redirect_url' => isset( $redirect_link ) ? $redirect_link : false,
						)
					);
					break;

				default:
					break;
			}
		}

		wp_die();
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

		wp_send_json(
			array(
				'code' => 1,
				'msg'  => esc_html__( 'Reset advanced settings successful.', 'inactive-logout' ),
			)
		);
		wp_die();
	}

	/**
	 * Adding Dialog in footer
	 */
	public function dialog_modal() {
		require_once INACTIVE_LOGOUT_VIEWS . '/tpl-inactive-logout-dialog.php';
	}

}

new Inactive_Logout_Functions();
