<?php
//Not Permission to agree more or less then given
if( !defined('ABSPATH') ) {
	die( '-1' );
}

/**
 * Main Class Defined
 *
 * @since  1.0.0
 * @author  Deepen
 */
final class Inactive__Logout_Main {

	const INA_VERSION = '1.4.1';

	const DEEPEN_URL = 'https://deepenbajracharya.com.np';

	public $plugin_dir;
	public $plugin_path;
	public $plugin_url;
	public $plugin_name;

	protected static $instance;

	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			$className      = __CLASS__;
			self::$instance = new $className;
		}
		return self::$instance;
	}

	protected function __construct() {
		$this->pluginPath = $this->plugin_path = trailingslashit( dirname( plugin_dir_path( __FILE__ ) ) );
		$this->pluginDir  = $this->plugin_dir = trailingslashit( basename( $this->plugin_path ) );
		$this->pluginUrl  = $this->plugin_url = plugins_url( $this->plugin_dir );

		add_action( 'init', array( $this, 'ina_loadTextDomain' ) );
		$this->ina_plugins_loaded();
	}

	/**
	* plugin activation callback
	* @see register_deactivation_hook()
	*
	* @param bool $network_deactivating
	*/
	public static function ina_activate() {
		if (function_exists('is_multisite') && is_multisite()) {
			global $wpdb;
			$old_blog = $wpdb->blogid;

      // Get all blog ids
			$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
			foreach ($blogids as $blog_id) {
				switch_to_blog($blog_id);
				self::_ina_activate_multisite();
			}
			switch_to_blog($old_blog);
			return;
		} else {
			self::instance()->_ina_activate_multisite();
		}

		//Load Necessary Components after activation
		self::instance()->ina_plugins_loaded();

		Inactive__ConcurrentLogins_functions::ina_destroy_all_old_sessions();
	}

	protected function _ina_activate_multisite() {
		$time = 15 * 60; //15 Minutes
		update_option( '__ina_logout_time', $time );
		update_option( '__ina_popup_overlay_color', '#000000' );
		update_option( '__ina_logout_message', '<p>You are being timed-out out due to inactivity. Please choose to stay signed in or to logoff.</p><p>Otherwise, you will be logged off automatically.</p>' );
		update_option( '__ina_warn_message', '<h3>Wakeup !</h3><p>You have been inactive for {wakup_timout}. Press continue to continue browsing.</p>' );
	}

	public static function ina_deactivate() {
		if (function_exists('is_multisite') && is_multisite()) {
			global $wpdb;
			$old_blog = $wpdb->blogid;
     	// Get all blog ids
			$blogids = $wpdb->get_col("SELECT blog_id FROM {$wpdb->blogs}");
			foreach ($blogids as $blog_id) {
				switch_to_blog($blog_id);
				delete_option( '__ina_logout_time' );
				delete_option( '__ina_logout_message' );
				delete_option( '__ina_warn_message' );
				delete_option( '__ina_enable_redirect' );
				delete_option( '__ina_redirect_page_link' );
			}
			switch_to_blog($old_blog);
			return;
		} else {
			delete_option( '__ina_logout_time' );
			delete_option( '__ina_logout_message' );
			delete_option( '__ina_warn_message' );
			delete_option( '__ina_enable_redirect' );
			delete_option( '__ina_redirect_page_link' );
		}
	}

	protected function ina_plugins_loaded() {
		$popup_overlay = get_option( '__ina_popup_overlay_color' );
		if(!$popup_overlay) {
			update_option( '__ina_popup_overlay_color', '#000000' );
		}

		if( is_user_logged_in() ) {
			if ( $this->ina_supportedVersion( 'wordpress' ) && $this->ina_supportedVersion( 'php' ) ) {
				$this->ina_addHooks();
				$this->ina_loadDependencies();
				$this->ina_define_them_constants();
				$this->ina_check_session();
			} else {
				// Either PHP or WordPress version is inadequate so we simply return an error.
				$this->ina_display_notSupportedError();
			}
		}

	}

	/**
	* Define Constant Values
	*/
	public function ina_define_them_constants() {
		$ina_helpers = Inactive__logout__Helpers::instance();
		$ina_helpers->ina_define( 'INACTIVE_LOGOUT_VERSION', self::INA_VERSION );
		$ina_helpers->ina_define( 'INACTIVE_LOGOUT_SLUG', 'ina-logout' );
		$ina_helpers->ina_define( 'INACTIVE_LOGOUT_VIEWS', $this->plugin_path . 'views' );
		$ina_helpers->ina_define( 'INACTIVE_LOGOUT_ASSETS_URL', $this->plugin_url . 'assets/' );
	}

	/**
	 * Require Dependencies files
	 */
	protected function ina_loadDependencies() {
		// Loading Helpers
		require_once $this->plugin_path . 'src/inactive-logout-helpers.php';

		//Loading Admin Views
		require_once $this->plugin_path . 'src/inactive-logout-admin-views.php';
		require_once $this->plugin_path . 'src/inactive-logout-functions.php';

		$concurrent = get_option( '__ina_concurrent_login' );
		if( isset($concurrent) == 1) {
			require_once $this->plugin_path . 'src/inactive-logout-concurrent-functions.php';
		}

	}

	/**
	* Add filters and actions
	*/
	protected function ina_addHooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'ina_adminScripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'ina_adminScripts' ) );
	}

	/**
	 * Loading Backend Scripts
	 */
	public function ina_adminScripts($hook_suffix) {
		if( is_user_logged_in() ) {
			$helper = Inactive__logout__Helpers::instance();
			$disable_timeoutjs = $helper->ina_check_user_role();
			if( !$disable_timeoutjs ) {
				wp_enqueue_script( INACTIVE_LOGOUT_SLUG . '-js', INACTIVE_LOGOUT_ASSETS_URL . 'js/inactive-logout.js', array('jquery'), time(), true );
			}

			if( $hook_suffix == 'settings_page_inactive-logout' ) {
				wp_enqueue_script( INACTIVE_LOGOUT_SLUG . '-inactive-logoutonly-js', INACTIVE_LOGOUT_ASSETS_URL . 'js/inactive-logout-other.js', array('jquery', 'wp-color-picker'), time(), true );
				wp_enqueue_script( INACTIVE_LOGOUT_SLUG . '-inactive-select-js', INACTIVE_LOGOUT_ASSETS_URL . 'js/select2.min.js', array('jquery'), time(), true );

				wp_enqueue_style( INACTIVE_LOGOUT_SLUG . '-inactive-select', INACTIVE_LOGOUT_ASSETS_URL . 'css/select2.min.css' , false, time() );
				wp_localize_script( INACTIVE_LOGOUT_SLUG . '-inactive-logoutonly-js', 'ina_other_ajax', array( 'ajaxurl' => admin_url('admin-ajax.php'), 'ina_security' => wp_create_nonce( "_ina_nonce_security" ) ));
			}
			wp_enqueue_style( INACTIVE_LOGOUT_SLUG, INACTIVE_LOGOUT_ASSETS_URL . 'css/inactive-logout.css' , false, time() );

			wp_localize_script( INACTIVE_LOGOUT_SLUG .'-js', 'ina_ajax', array( 'ajaxurl' => admin_url('admin-ajax.php'), 'ina_security' => wp_create_nonce( "_checklastSession" ) ));
		}
	}

	/**
	* Test PHP and WordPress versions for compatibility
	*
	* @param string $checking - checking to be tested such as 'php' or 'wordpress'
	*
	* @return boolean - is the existing version of the checking supported?
	*/
	public function ina_supportedVersion( $checking ) {
		switch ( strtolower( $checking ) ) {
			case 'wordpress':
			$supported = version_compare( get_bloginfo( 'version' ), '4.0', '>=' );
			break;
			case 'php':
			$supported = version_compare( phpversion(), '5.2', '>=' );
			break;
		}

		return $supported;
	}

	/**
	* Display a WordPress or PHP incompatibility error
	*/
	public function ina_display_notSupportedError() {
		if ( ! $this->ina_supportedVersion( 'wordpress' ) ) {
			echo '<p>' . sprintf( esc_html__( 'Sorry, Inactive User Logout requires WordPress %s or higher. Please upgrade your WordPress install.', 'ina-logout' ), '4.0' ) . '</p>';
			exit;
		}
		if ( ! $this->ina_supportedVersion( 'php' ) ) {
			echo '<p>' . sprintf( esc_html__( 'Sorry, Inactive User Logout requires PHP %s or higher. Talk to your Web host about moving you to a newer version of PHP.', 'ina-logout' ), '5.4' ) . '</p>';
			exit;
		}
	}

	/**
	* Load the text domain.
	*/
	public function ina_loadTextDomain() {
		$domain = 'ina-logout';
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
		load_plugin_textdomain( $domain, false, $this->plugin_dir . 'lang/' );
	}

	/**
	 * Check Last Session. Logout if seesion is old
	 */
	public function ina_check_session() {
		$user_session = get_user_meta( get_current_user_id(), '__ina_last_active_session', true );
		$logout_minutes = get_option( '__ina_logout_time' );
		if( !empty($user_session) ) {
			$logout = $user_session + $logout_minutes;
			if( time() >= $logout ) {
				wp_logout();
				delete_user_meta( get_current_user_id(), '__ina_last_active_session' );
			}
		}
	}

}