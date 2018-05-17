<?php
/**
 * File contains functions for Logout settings.
 *
 * @package inactive-logout
 */

// Not Permission to agree more or less then given.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Main Class Defined
 *
 * @since   1.0.0
 * @author  Deepen
 */
final class Inactive_Logout_Main {

	const INA_VERSION = '1.7.5';

	/**
	 * Directory of plugin.
	 *
	 * @var $plugin_dir
	 */
	public $plugin_dir;

	/**
	 * Plugin filesystem directory path
	 *
	 * @var $plugin_path
	 */
	public $plugin_path;

	/**
	 * Plugin directory url.
	 *
	 * @var $plugin_url
	 */
	public $plugin_url;

	/**
	 * Plugin name.
	 *
	 * @var $plugin_name
	 */
	public $plugin_name;

	/**
	 * Class instance.
	 *
	 * @access protected
	 *
	 * @var $instance
	 */
	protected static $instance;

	/**
	 * Return class instance.
	 *
	 * @return static Instance of class.
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Inactive_Logout_Main constructor.
	 */
	protected function __construct() {
		$this->plugin_path = trailingslashit( dirname( plugin_dir_path( __FILE__ ) ) );
		$this->plugin_dir  = trailingslashit( basename( $this->plugin_path ) );
		$this->plugin_url  = plugins_url( $this->plugin_dir );

		add_action( 'init', array( $this, 'ina_load_text_domain' ) );
		$this->ina_plugins_loaded();
	}

	/**
	 * Plugin activation callback.
	 *
	 * @see register_deactivation_hook()
	 */
	public static function ina_activate() {
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			global $wpdb;
			$old_blog = $wpdb->blogid;

			// Get all blog ids.
			$blogids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" ); // WPCS: db call ok, cache ok.
			foreach ( $blogids as $blog_id ) {
				switch_to_blog( $blog_id );
				self::instance()->_ina_activate_multisite();
			}
			switch_to_blog( $old_blog );

			return;
		} else {
			self::instance()->_ina_activate_multisite();
		}

		// Load Necessary Components after activation.
		self::instance()->ina_plugins_loaded();
	}

	/**
	 * Saving options for multisite.
	 */
	protected function _ina_activate_multisite() {
		$time = 15 * 60; // 15 Minutes
		update_option( '__ina_logout_time', $time );
		update_option( '__ina_popup_overlay_color', '#000000' );
		update_option( '__ina_logout_message', '<p>You are being timed-out out due to inactivity. Please choose to stay signed in or to logoff.</p><p>Otherwise, you will be logged off automatically.</p>' );
		update_option( '__ina_warn_message', '<h3>Wakeup !</h3><p>You have been inactive for {wakup_timout}. Press continue to continue browsing.</p>' );
	}

	/**
	 * Managing things when plugin is deactivated.
	 */
	public static function ina_deactivate() {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			global $wpdb;
			$old_blog = $wpdb->blogid;

			// Get all blog ids.
			$blogids = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" ); // WPCS: db call ok, cache ok.

			foreach ( $blogids as $blog_id ) {
				switch_to_blog( $blog_id );
				delete_option( '__ina_logout_time' );
				delete_option( '__ina_logout_message' );
				delete_option( '__ina_warn_message' );
				delete_option( '__ina_enable_redirect' );
				delete_option( '__ina_redirect_page_link' );

				delete_site_option( '__ina_overrideby_multisite_setting' );
				delete_site_option( '__ina_logout_time' );
				delete_site_option( '__ina_logout_message' );
				delete_site_option( '__ina_warn_message' );
				delete_site_option( '__ina_enable_redirect' );
				delete_site_option( '__ina_redirect_page_link' );
			}
			switch_to_blog( $old_blog );

			return;
		} else {
			delete_option( '__ina_logout_time' );
			delete_option( '__ina_logout_message' );
			delete_option( '__ina_warn_message' );
			delete_option( '__ina_enable_redirect' );
			delete_option( '__ina_redirect_page_link' );
		}
	}

	/**
	 * Manging things when plugin is loaded.
	 */
	protected function ina_plugins_loaded() {
		$popup_overlay = get_option( '__ina_popup_overlay_color' );
		if ( ! $popup_overlay ) {
			update_option( '__ina_popup_overlay_color', '#000000' );
		}

		if ( is_user_logged_in() ) {
			if ( $this->ina_supported_version( 'WordPress' ) && $this->ina_supported_version( 'php' ) ) {
				$this->ina_add_hooks();
				$this->ina_load_dependencies();
				$this->ina_define_them_constants();
			} else {
				// Either PHP or WordPress version is inadequate so we simply return an error.
				$this->ina_display_not_supported_error();
			}
		}

	}

	/**
	 * Define Constant Values
	 */
	public function ina_define_them_constants() {
		$ina_helpers = Inactive_Logout_Helpers::instance();
		$ina_helpers->ina_define( 'INACTIVE_LOGOUT_VERSION', self::INA_VERSION );
		$ina_helpers->ina_define( 'INACTIVE_LOGOUT_SLUG', 'inactive-logout' );
		$ina_helpers->ina_define( 'INACTIVE_LOGOUT_VIEWS', $this->plugin_path . 'views' );
		$ina_helpers->ina_define( 'INACTIVE_LOGOUT_ASSETS_URL', $this->plugin_url . 'assets/' );
	}

	/**
	 * Require Dependencies files.
	 */
	protected function ina_load_dependencies() {
		// Loading Helpers.
		require_once $this->plugin_path . 'src/class-inactive-logout-helpers.php';

		// Loading Admin Views.
		require_once $this->plugin_path . 'src/class-inactive-logout-admin-views.php';
		require_once $this->plugin_path . 'src/class-inactive-logout-functions.php';

		$concurrent = get_option( '__ina_concurrent_login' );

		// Checking if advanced settings are enabled
		// @added from 1.6.0.
		$ina_multiuser_timeout_enabled = get_option( '__ina_enable_timeout_multiusers' );
		if ( ! empty( $ina_multiuser_timeout_enabled ) ) {
			$helper                   = Inactive_Logout_Helpers::instance();
			$disable_concurrent_login = $helper->ina_check_user_role_concurrent_login();
			if ( $disable_concurrent_login ) {
				require_once $this->plugin_path . 'src/class-inactive-concurrent-login-functions.php';
			}
		} else {
			if ( isset( $concurrent ) && 1 === intval( $concurrent ) ) {
				require_once $this->plugin_path . 'src/class-inactive-concurrent-login-functions.php';
			}
		}
	}

	/**
	 * Add filters and actions
	 */
	protected function ina_add_hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'ina_admin_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'ina_admin_scripts' ) );
	}

	/**
	 * Loading Backend Scripts.
	 *
	 * @param string $hook_suffix Suffix for hooks.
	 */
	public function ina_admin_scripts() {
		global $current_user;

		if ( is_user_logged_in() ) {
			// Check if multisite.
			$override = is_multisite() ? get_site_option( '__ina_overrideby_multisite_setting' ) : false;
			if ( ! empty( $override ) ) {
				$ina_logout_time          = get_site_option( '__ina_logout_time' ) ? get_site_option( '__ina_logout_time' ) : null;
				$idle_disable_countdown   = get_site_option( '__ina_disable_countdown' ) ? get_site_option( '__ina_disable_countdown' ) : null;
				$ina_warn_message_enabled = get_site_option( '__ina_warn_message_enabled' ) ? get_site_option( '__ina_warn_message_enabled' ) : null;

				$ina_multiuser_timeout_enabled = get_site_option( '__ina_enable_timeout_multiusers' );
				if ( $ina_multiuser_timeout_enabled ) {
					$ina_multiuser_settings = get_site_option( '__ina_multiusers_settings' );
					foreach ( $ina_multiuser_settings as $ina_multiuser_setting ) {
						if ( in_array( $ina_multiuser_setting['role'], $current_user->roles, true ) ) {
							$ina_logout_time = $ina_multiuser_setting['timeout'] * 60; // Seconds.
						}
					}
				}
			} else {
				$ina_logout_time          = get_option( '__ina_logout_time' ) ? get_option( '__ina_logout_time' ) : null;
				$idle_disable_countdown   = get_option( '__ina_disable_countdown' ) ? get_option( '__ina_disable_countdown' ) : null;
				$ina_warn_message_enabled = get_option( '__ina_warn_message_enabled' ) ? get_option( '__ina_warn_message_enabled' ) : null;

				$ina_multiuser_timeout_enabled = get_option( '__ina_enable_timeout_multiusers' );
				if ( $ina_multiuser_timeout_enabled ) {
					$ina_multiuser_settings = get_option( '__ina_multiusers_settings' );
					foreach ( $ina_multiuser_settings as $ina_multiuser_setting ) {
						if ( in_array( $ina_multiuser_setting['role'], $current_user->roles, true ) ) {
							$ina_logout_time = $ina_multiuser_setting['timeout'] * 60; // Seconds.
						}
					}
				}
			}

			$ina_meta_data                             = array();
			$ina_meta_data['ina_timeout']              = ( isset( $ina_logout_time ) ) ? $ina_logout_time : 15 * 60;
			$ina_meta_data['ina_disable_countdown']    = ( isset( $idle_disable_countdown ) && 1 === intval( $idle_disable_countdown ) ) ? $idle_disable_countdown : false;
			$ina_meta_data['ina_warn_message_enabled'] = ( isset( $ina_warn_message_enabled ) && 1 === intval( $ina_warn_message_enabled ) ) ? $ina_warn_message_enabled : false;

			$helper            = Inactive_Logout_Helpers::instance();
			$disable_timeoutjs = $helper->ina_check_user_role();
			if ( ! $disable_timeoutjs ) {
				wp_enqueue_script( 'ina-logout-js', INACTIVE_LOGOUT_ASSETS_URL . 'js/inactive-logout.js', array( 'jquery' ), time(), true );
				wp_localize_script( 'ina-logout-js', 'ina_meta_data', $ina_meta_data );

				wp_localize_script(
					'ina-logout-js', 'ina_ajax', array(
						'ajaxurl'      => admin_url( 'admin-ajax.php' ),
						'ina_security' => wp_create_nonce( '_checklastSession' ),
					)
				);
			}

			wp_register_script( 'ina-logout-inactive-logoutonly-js', INACTIVE_LOGOUT_ASSETS_URL . 'js/inactive-logout-other.js', array( 'jquery', 'wp-color-picker' ), time(), true );
			wp_register_script( 'ina-logout-inactive-select-js', INACTIVE_LOGOUT_ASSETS_URL . 'js/select2.min.js', array( 'jquery' ), time(), true );

			wp_register_style( 'ina-logout-inactive-select', INACTIVE_LOGOUT_ASSETS_URL . 'css/select2.min.css', false, time() );
			wp_localize_script(
				'ina-logout-inactive-logoutonly-js', 'ina_other_ajax', array(
					'ajaxurl'      => admin_url( 'admin-ajax.php' ),
					'ina_security' => wp_create_nonce( '_ina_nonce_security' ),
				)
			);

			wp_enqueue_style( 'ina-logout', INACTIVE_LOGOUT_ASSETS_URL . 'css/inactive-logout.css', false, time() );
		}
	}

	/**
	 * Test PHP and WordPress versions for compatibility
	 *
	 * @param string $checking - checking to be tested such as 'php' or 'WordPress'.
	 *
	 * @return boolean - is the existing version of the checking supported?
	 */
	function ina_supported_version( $checking ) {
		$supported = false;

		switch ( strtolower( $checking ) ) {
			case 'wordpress': // WPCS: spelling ok.
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
	function ina_display_not_supported_error() {
		if ( ! $this->ina_supported_version( 'WordPress' ) ) {
			// translators: Minimum required WordPress version.
			echo '<p>' . sprintf( esc_html__( 'Sorry, Inactive User Logout requires WordPress %s or higher. Please upgrade your WordPress install.', 'inactive-logout' ), '4.0' ) . '</p>';
			exit;
		}
		if ( ! $this->ina_supported_version( 'php' ) ) {
			// translators: Minimum required PHP version.
			echo '<p>' . sprintf( esc_html__( 'Sorry, Inactive User Logout requires PHP %s or higher. Talk to your Web host about moving you to a newer version of PHP.', 'inactive-logout' ), '5.4' ) . '</p>';
			exit;
		}
	}

	/**
	 * Load the text domain.
	 */
	function ina_load_text_domain() {
		$domain = 'inactive-logout';
		apply_filters( 'plugin_locale', get_locale(), $domain );
		load_plugin_textdomain( $domain, false, $this->plugin_dir . 'lang/' );
	}
}
