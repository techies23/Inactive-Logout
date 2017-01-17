<?php
//Not Permission to agree more or less then given
if( !defined('ABSPATH') ) {
	die( '-1' );
}

/**
 * Admin Viws Class
 *
 * @since  1.0.0
 * @author  Deepen
 */
class Inactive__Logout_adminViews {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'ina_create_options_menu' ) );
	}

	/**
	 * Add a Menu Option in settings 
	 */
	public function ina_create_options_menu() {
		add_options_page( 
			__("Inactive User Logout Settings", "ina-logout"),
			__("Inactive Logout", "ina-logout"),
			'manage_options', 
			'inactive-logout',
			array( $this, 'ina__render_options' )
			);
	}

	/** Rendering the output */
	public function ina__render_options() {	
		if( isset($_POST['submit']) && ! wp_verify_nonce( $_POST['_save_timeout_settings'], '_nonce_action_save_timeout_settings' ) ) {
			wp_die("Not Allowed");
			exit;
		} 

		$saved = false;
		if( isset($_POST['submit']) ) {
			$idle_timeout = filter_input( INPUT_POST, 'idle_timeout', FILTER_SANITIZE_NUMBER_INT );
			$idle_timeout_message = wp_kses_post( filter_input(INPUT_POST, 'idle_message_text') );
			$idle_disable_countdown = filter_input(INPUT_POST, 'idle_disable_countdown', FILTER_SANITIZE_NUMBER_INT);
			$ina_show_warn_message_only = filter_input(INPUT_POST, 'ina_show_warn_message_only', FILTER_SANITIZE_NUMBER_INT);
			$ina_show_warn_message = wp_kses_post( filter_input(INPUT_POST, 'ina_show_warn_message') );
			$save_minutes = $idle_timeout * 60; //Minutes
			if($idle_timeout) {
				update_option( '__ina_logout_time', $save_minutes );
				update_option( '__ina_logout_message', $idle_timeout_message );
				update_option( '__ina_disable_countdown', $idle_disable_countdown );
				update_option( '__ina_warn_message_enabled', $ina_show_warn_message_only );
				update_option( '__ina_warn_message', $ina_show_warn_message );

				$saved = true;

				$helper = Inactive__logout__Helpers::instance();
				$helper->ina_reload();
			}
		}

		$time = get_option( '__ina_logout_time' );
		$countdown_enable = get_option( '__ina_disable_countdown' );
		$ina_warn_message_enabled = get_option( '__ina_warn_message_enabled' );
		require_once INACTIVE_LOGOUT_VIEWS . '/tpl-inactive-logout-settings.php';
	}

}
new Inactive__Logout_adminViews();

