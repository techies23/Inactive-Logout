<?php
/**
 * File contains class related to Admin views.
 *
 * @package inactive-logout
 */

// Not Permission to agree more or less then given.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Admin Views Class
 *
 * @since   1.0.0
 * @author  Deepen
 */
class Inactive_Logout_Admin_Views {

	/**
	 * Helper.
	 *
	 * @var Inactive_Logout_Helpers
	 */
	public $helper;

	public static $message = '';

	public $settings;

	/**
	 * Inactive_Logout_Admin_Views constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'ina_create_options_menu' ) );

		// Add Menu for multisite network.
		add_action( 'network_admin_menu', array( $this, 'ina_menu_multisite_network' ) );

		add_action( 'ina_before_settings_wrapper', array( $this, 'ina_before_settings_wrap' ) );
		add_action( 'ina_after_settings_wrapper', array( $this, 'ina_after_settings_wrap' ) );

		$this->helper = Inactive_Logout_Helpers::instance();
	}

	/**
	 * Add a Menu Option in settings
	 */
	public function ina_create_options_menu() {
		if ( is_multisite() ) {
			$idle_overrideby_multisite_setting = get_site_option( '__ina_overrideby_multisite_setting' );
			if ( empty( $idle_overrideby_multisite_setting ) ) {
				add_options_page(
					__( 'Inactive User Logout Settings', 'inactive-logout' ),
					__( 'Inactive Logout', 'inactive-logout' ),
					'manage_options',
					'inactive-logout',
					array( $this, 'ina__render_options' )
				);
			}
		} else {
			add_options_page(
				__( 'Inactive User Logout Settings', 'inactive-logout' ),
				__( 'Inactive Logout', 'inactive-logout' ),
				'manage_options',
				'inactive-logout',
				array( $this, 'ina__render_options' )
			);
		}
	}

	/**
	 * Add menu page.
	 */
	function ina_menu_multisite_network() {
		add_menu_page(
			__( 'Inactive User Logout Settings', 'inactive-logout' ),
			__( 'Inactive Logout', 'inactive-logout' ),
			'manage_options',
			'inactive-logout',
			array( $this, 'ina__render_options' )
		);
	}

	/**
	 * Rendering the output.
	 */
	public function ina__render_options() {
		$saved = false;

		$submit = filter_input( INPUT_POST, 'submit', FILTER_SANITIZE_STRING );

		if ( isset( $submit ) ) {
			$this->ina__process_basic_settings();
		}

		$adv_submit = filter_input( INPUT_POST, 'adv_submit', FILTER_SANITIZE_STRING );

		if ( isset( $adv_submit ) ) {
			$this->ina__process_adv_settings();
		}

		// Css rules for Color Picker.
		wp_enqueue_style( 'wp-color-picker' );
		$tab        = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_STRING );
		$active_tab = isset( $tab ) ? $tab : 'ina-basic';

		// Include Template.
		do_action( 'ina_before_settings_wrapper' );
		require_once INACTIVE_LOGOUT_VIEWS . '/tpl-inactive-logout-settings.php';
		if ( 'ina-basic' === $active_tab ) {
			// BASIC.
			if ( is_network_admin() && is_multisite() ) {
				$idle_overrideby_multisite_setting = get_site_option( '__ina_overrideby_multisite_setting' );
			}

			$time                     = get_option( '__ina_logout_time' );
			$warn_time                = get_option( '__ina_warn_time' );
			$countdown_enable         = get_option( '__ina_disable_countdown' );
			$ina_warn_message_enabled = get_option( '__ina_warn_message_enabled' );
			$ina_concurrent           = get_option( '__ina_concurrent_login' );
			$ina_full_overlay         = get_option( '__ina_full_overlay' );
			$ina_popup_overlay_color  = get_option( '__ina_popup_overlay_color' );
			$ina_enable_redirect      = get_option( '__ina_enable_redirect' );
			$ina_redirect_page_link   = get_option( '__ina_redirect_page_link' );

			// IF redirect is custom page link.
			if ( 'custom-page-redirect' === $ina_redirect_page_link ) {
				$custom_redirect_text_field = get_option( '__ina_custom_redirect_text_field' );
			}

			require_once INACTIVE_LOGOUT_VIEWS . '/tabs/tpl-inactive-logout-basic.php';
		} else {
			// ADVANCED.
			$ina_multiuser_timeout_enabled = get_option( '__ina_enable_timeout_multiusers' );
			if ( $ina_multiuser_timeout_enabled ) {
				$ina_multiuser_settings = get_option( '__ina_multiusers_settings' );
			}

			require_once INACTIVE_LOGOUT_VIEWS . '/tabs/tpl-inactive-logout-advanced.php';
		}

		do_action( 'ina_after_settings_wrapper' );
	}

	/**
	 * Manages Basic settings.
	 *
	 * @return bool|void
	 */
	public function ina__process_basic_settings() {

		$sm_nonce = filter_input( INPUT_POST, '_save_timeout_settings', FILTER_SANITIZE_STRING );
		$nonce    = isset( $sm_nonce ) ? $sm_nonce : '';
		$submit   = filter_input( INPUT_POST, 'submit', FILTER_SANITIZE_STRING );

		if ( isset( $submit ) && ! wp_verify_nonce( $nonce, '_nonce_action_save_timeout_settings' ) ) {
			wp_die( 'Not Allowed' );

			return;
		}

		$idle_timeout               = filter_input( INPUT_POST, 'idle_timeout', FILTER_SANITIZE_NUMBER_INT );
		$idle_warn_time            = filter_input( INPUT_POST, 'idle_warn_time', FILTER_SANITIZE_NUMBER_INT );
		$idle_timeout_message       = wp_kses_post( filter_input( INPUT_POST, 'idle_message_text' ) );
		$idle_disable_countdown     = filter_input( INPUT_POST, 'idle_disable_countdown', FILTER_SANITIZE_NUMBER_INT );
		$ina_show_warn_message_only = filter_input( INPUT_POST, 'ina_show_warn_message_only', FILTER_SANITIZE_NUMBER_INT );
		$ina_show_warn_message      = wp_kses_post( filter_input( INPUT_POST, 'ina_show_warn_message' ) );
		$ina_disable_multiple_login = filter_input( INPUT_POST, 'ina_disable_multiple_login', FILTER_SANITIZE_NUMBER_INT );

		$ina_background_popup = trim( filter_input( INPUT_POST, 'ina_color_picker' ) );
		$ina_background_popup = strip_tags( stripslashes( $ina_background_popup ) );

		$ina_full_overlay         = filter_input( INPUT_POST, 'ina_full_overlay', FILTER_SANITIZE_NUMBER_INT );
		$ina_enable_redirect_link = filter_input( INPUT_POST, 'ina_enable_redirect_link', FILTER_SANITIZE_NUMBER_INT );
		$ina_redirect_page        = filter_input( INPUT_POST, 'ina_redirect_page' );

		if ( 'custom-page-redirect' === $ina_redirect_page ) {
			$ina_custom_redirect_text_field = filter_input( INPUT_POST, 'custom_redirect_text_field' );
		}

		do_action( 'ina_before_update_basic_settings' );

		// If Mulisite is Active then Add these settings to mulsite option table as well.
		if ( is_network_admin() && is_multisite() ) {
			$idle_overrideby_multisite_setting = filter_input( INPUT_POST, 'idle_overrideby_multisite_setting', FILTER_SANITIZE_NUMBER_INT );
			update_site_option( '__ina_overrideby_multisite_setting', $idle_overrideby_multisite_setting );

			$save_minutes = $idle_timeout * 60; // 60 minutes
			if ( $idle_timeout ) {
				update_site_option( '__ina_logout_time', $save_minutes );
				update_site_option( '__ina_warn_time', $idle_warn_time );
				update_site_option( '__ina_logout_message', $idle_timeout_message );
				update_site_option( '__ina_disable_countdown', $idle_disable_countdown );
				update_site_option( '__ina_warn_message_enabled', $ina_show_warn_message_only );
				update_site_option( '__ina_warn_message', $ina_show_warn_message );
				update_site_option( '__ina_concurrent_login', $ina_disable_multiple_login );
				update_site_option( '__ina_full_overlay', $ina_full_overlay );
				update_site_option( '__ina_popup_overlay_color', $ina_background_popup );
				update_site_option( '__ina_enable_redirect', $ina_enable_redirect_link );
				update_site_option( '__ina_redirect_page_link', $ina_redirect_page );

				if ( 'custom-page-redirect' === $ina_redirect_page ) {
					update_site_option( '__ina_custom_redirect_text_field', $ina_custom_redirect_text_field );
				}
			}
		}

		$save_minutes = $idle_timeout * 60; // 60 minutes
		if ( $idle_timeout ) {
			update_option( '__ina_logout_time', $save_minutes );
			update_option( '__ina_warn_time', $idle_warn_time );
			update_option( '__ina_logout_message', $idle_timeout_message );
			update_option( '__ina_disable_countdown', $idle_disable_countdown );
			update_option( '__ina_warn_message_enabled', $ina_show_warn_message_only );
			update_option( '__ina_warn_message', $ina_show_warn_message );
			update_option( '__ina_concurrent_login', $ina_disable_multiple_login );
			update_option( '__ina_full_overlay', $ina_full_overlay );
			update_option( '__ina_popup_overlay_color', $ina_background_popup );
			update_option( '__ina_enable_redirect', $ina_enable_redirect_link );
			update_option( '__ina_redirect_page_link', $ina_redirect_page );

			if ( 'custom-page-redirect' === $ina_redirect_page ) {
				update_option( '__ina_custom_redirect_text_field', $ina_custom_redirect_text_field );
			}
		}

		do_action( 'ina_after_update_basic_settings' );

		self::set_message( 'updated', 'Settings Saved !' );
	}

	/**
	 * Manages Advance settings.
	 *
	 * @return bool|void
	 */
	public function ina__process_adv_settings() {

		$sm_nonce   = filter_input( INPUT_POST, '_save_timeout_adv_settings', FILTER_SANITIZE_STRING );
		$nonce      = isset( $sm_nonce ) ? $sm_nonce : '';
		$adv_submit = filter_input( INPUT_POST, 'adv_submit', FILTER_SANITIZE_STRING );

		if ( isset( $adv_submit ) && ! wp_verify_nonce( $nonce, '_nonce_action_save_timeout_adv_settings' ) ) {
			wp_die( 'Not Allowed' );

			return;
		}

		$ina_enable_different_role_timeout     = filter_input( INPUT_POST, 'ina_enable_different_role_timeout' );
		$ina_multiuser_roles                   = filter_input( INPUT_POST, 'ina_multiuser_roles', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY );
		$ina_individual_user_timeout           = filter_input( INPUT_POST, 'ina_individual_user_timeout', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY );
		$ina_redirect_page_individual_user     = filter_input( INPUT_POST, 'ina_redirect_page_individual_user', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY );
		$ina_disable_inactive_logout           = filter_input( INPUT_POST, 'ina_disable_inactive_logout', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY );
		$ina_disable_inactive_concurrent_login = filter_input( INPUT_POST, 'ina_disable_inactive_concurrent_login', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY );

		$container_multi_user_arr = array();
		if ( $ina_multiuser_roles ) {
			foreach ( $ina_multiuser_roles as $k => $ina_multiuser_role ) {
				$user_timeout_minutes              = ! empty( $ina_individual_user_timeout[ $k ] ) ? $ina_individual_user_timeout[ $k ] : 15;
				$multi_userredirect_page_link      = ! empty( $ina_redirect_page_individual_user[ $k ] ) ? $ina_redirect_page_individual_user[ $k ] : null;
				$disabled_for_user                 = ! empty( $ina_disable_inactive_logout[ $ina_multiuser_role ] ) ? 1 : null;
				$disabled_for_user_concurent_login = ! empty( $ina_disable_inactive_concurrent_login[ $ina_multiuser_role ] ) ? 1 : null;
				$container_multi_user_arr[]        = array(
					'role'                      => $ina_multiuser_role,
					'timeout'                   => $user_timeout_minutes,
					'redirect_page'             => $multi_userredirect_page_link,
					'disabled_feature'          => $disabled_for_user,
					'disabled_concurrent_login' => $disabled_for_user_concurent_login,
				);
			}
		}

		do_action( 'ina_before_update_adv_settings', $container_multi_user_arr );

		if ( is_network_admin() && is_multisite() ) {
			update_site_option( '__ina_enable_timeout_multiusers', $ina_enable_different_role_timeout );
			if ( $ina_enable_different_role_timeout ) {
				update_site_option( '__ina_multiusers_settings', $container_multi_user_arr );
			}
		}

		update_option( '__ina_enable_timeout_multiusers', $ina_enable_different_role_timeout );
		if ( $ina_enable_different_role_timeout ) {
			update_option( '__ina_multiusers_settings', $container_multi_user_arr );
		}

		do_action( 'ina_after_update_adv_settings' );

		self::set_message( 'updated', 'Settings Saved !' );
	}

	/**
	 * Settings wrapper html element.
	 */
	public function ina_before_settings_wrap() {
		echo '<div id="ina-cover-loading" style="display: none;"></div><div class="wrap">';
	}

	/**
	 * Settings wrapper html element.
	 */
	public function ina_after_settings_wrap() {
		echo '</div>';
	}

	static function get_message() {
		return self::$message;
	}

	static function set_message( $class, $message ) {
		self::$message = '<div class=' . $class . '><p>' . $message . '</p></div>';
	}
}

new Inactive_Logout_Admin_Views();
