<?php
/**
 * File contains functions for logout helpers.
 *
 * @package inactive-logout
 */

// Don't load directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class with a few helpers
 */
class Inactive_Logout_Helpers {

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
	 * Define constant if not already set.
	 *
	 * @param string $name Constant name.
	 * @param string|bool $value Constant value.
	 *
	 * @since   2.0.0
	 *
	 * @author  Deepen Bajracharya
	 */
	public function ina_define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Convert seconds to minutes.
	 *
	 * @param int $value Number of seconds.
	 *
	 * @return string
	 */
	public function ina_convert_to_minutes( $value ) {
		$minutes = floor( $value / 60 );

		return $minutes . ' ' . esc_html__( 'Minute(s)', 'inactive-logout' );
	}

	/**
	 * Manages reloading page.
	 */
	public function ina_reload() {
		?>
        <script type="text/javascript">location.reload();</script>
		<?php
	}

	/**
	 * Get all roles.
	 *
	 * @return array List of roles.
	 */
	public function ina_get_all_roles() {
		$result = array();

		$roles = get_editable_roles();
		foreach ( $roles as $role => $role_name ) {
			$result[ $role ] = $role_name['name'];
		}

		return $result;
	}

	/**
	 * Get All Pages and Posts
	 *
	 * @return array
	 * @since  1.2.0
	 */
	public function ina_get_all_pages_posts() {
		$result = array();
		$pages  = get_posts( array(
			'order'          => 'ASC',
			'posts_per_page' => - 1,
			'post_type'      => apply_filters( 'ina_free_get_custom_post_types', array( 'post', 'page' ) ),
			'post_status'    => 'publish',
		) );

		if ( ! empty( $pages ) ) {
			foreach ( $pages as $page ) {
				$result[ $page->post_type ][] = array(
					'ID'        => $page->ID,
					'title'     => $page->post_title,
					'permalink' => get_the_permalink( $page->ID ),
					'post_type' => $page->post_type,
				);
			}
		}

		return $result;
	}

	/**
	 * Check role is available in settings for multi-user.
	 *
	 * @param null|string $role Name of role, default is null.
	 *
	 * @return bool Returns true if passed role is available, Otherwise false.
	 */
	public function ina_check_role_enabledfor_multiuser( $role = null ) {
		$selected = false;
		if ( ! empty( $role ) ) {
			$ina_multiuser_settings = $this->get_overrided_option( '__ina_multiusers_settings' );
			if ( ! empty( $ina_multiuser_settings ) ) {
				foreach ( $ina_multiuser_settings as $ina_multiuser_setting ) {
					if ( in_array( $role, $ina_multiuser_setting, true ) ) {
						$selected = true;
					}
				}
			}
		}

		return $selected;
	}

	/**
	 * Check to disable the Inactive for certain user role
	 *
	 * @return BOOL
	 * @author  Deepen
	 */
	public function ina_check_user_role() {
		$user                          = wp_get_current_user();
		$ina_roles                     = $this->get_overrided_option( '__ina_multiusers_settings' );
		$result                        = false;
		$ina_multiuser_timeout_enabled = $this->get_overrided_option( '__ina_enable_timeout_multiusers' );
		if ( $ina_roles && ! empty( $ina_multiuser_timeout_enabled ) ) {
			foreach ( $ina_roles as $role ) {
				if ( 1 == $role['disabled_feature'] ) {
					if ( in_array( $role['role'], (array) $user->roles, true ) ) {
						$result = true;
					}
				}
			}
		}

		return $result;
	}

	/**
	 * Check to disable the Inactive for certain user role
	 *
	 * @param $user
	 *
	 * @return bool
	 * @author  Deepen
	 * @since   1.6.0
	 *
	 */
	public function ina_check_user_role_concurrent_login( $user = false ) {
		if ( empty( $user ) ) {
			$user = wp_get_current_user();
		}

		$ina_roles = $this->get_overrided_option( '__ina_multiusers_settings' );
		$result    = false;
		if ( $ina_roles ) {
			foreach ( $ina_roles as $role ) {
				if ( ! empty( $role['disabled_concurrent_login'] ) && 1 === intval( $role['disabled_concurrent_login'] ) ) {
					if ( in_array( $role['role'], (array) $user->roles, true ) ) {
						$result = true;
					}
				}
			}
		}

		return $result;
	}

	public static function show_plugin_like() {
		?>
        <div class="notice notice-warning is-dismissible">
            <h3><?php esc_html_e( 'Like this plugin ?', 'inactive-logout' ); ?></h3>
            <p>
				<?php
				// translators: anchor tag.
				printf( esc_html__( 'Please consider giving a %s if you found this useful at wordpress.org.', 'inactive-logout' ), '<a href="https://wordpress.org/support/plugin/inactive-logout/reviews/#new-post">5 star thumbs up</a>' );
				?>
            </p>
        </div>
		<?php
	}

	/**
	 * Check if pro version is active
	 *
	 * @return bool
	 */
	public function is_pro_version_active() {
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		if ( is_plugin_active( 'inactive-logout-addon/inactive-logout-addon.php' ) ) {
			return true;
		} else {
			return false;
		}
	}

	public static function show_plugin_referrals() {
		?>
        <div id="message" class="notice notice-warning is-dismissible">
            <h3><?php esc_html_e( 'Need more features ?', 'inactive-logout' ); ?></h3>
            <p>Among many other features/enhancements, Inactive Logout pro comes with a additional features. <a href="https://www.codemanas.com/downloads/inactive-logout-pro/">Check out here</a> to learn more.</p>
        </div>
		<?php
	}

	public function show_advanced_enable_notification() {
		$ina_multiuser_timeout_enabled = $this->get_overrided_option( '__ina_enable_timeout_multiusers' );
		if ( ! empty( $ina_multiuser_timeout_enabled ) ) {
			?>
            <div id="message" class="notice notice-warning">
                <p><?php esc_html_e( 'Is inactive logout or few functionalities not working for you ? Might be because you have added this user role in Role Based tab ?', 'inactive-logout' ); ?></p>
            </div>
			<?php
		}
	}

	/**
	 * Get Option based on multisite or only one site
	 *
	 * @param $key
	 *
	 * @return mixed|void
	 */
	public function get_option( $key ) {
		if ( is_multisite() && is_network_admin() ) {
			$result = get_site_option( $key );
		} else {
			$result = get_option( $key );
		}

		return $result;
	}

	/**
	 * Get Overridden multisite setting
	 *
	 * @param $key
	 *
	 * @return mixed|void
	 */
	public function get_overrided_option( $key ) {
		if ( is_multisite() ) {
			$network_id = get_main_network_id();
			$override   = get_network_option( $network_id, '__ina_overrideby_multisite_setting' ) ? true : false;
			if ( $override ) {
				$result = get_network_option( $network_id, $key );
			} else {
				$result = $this->get_option( $key );
			}
		} else {
			$result = $this->get_option( $key );
		}

		return $result;
	}

	/**
	 * Update option
	 *
	 * @param $key
	 * @param $value
	 */
	public function update_option( $key, $value ) {
		if ( is_network_admin() && is_multisite() ) {
			update_site_option( $key, $value );
		} else {
			update_option( $key, $value );
		}
	}
}

function ina_helpers() {
	return Inactive_Logout_Helpers::instance();
}

ina_helpers();