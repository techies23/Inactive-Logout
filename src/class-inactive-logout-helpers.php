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
	 * @param  string      $name  Constant name.
	 * @param  string|bool $value Constant value.
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
	 * @since  1.2.0
	 * @return array
	 */
	public function ina_get_all_pages_posts() {
		$result = array();
		$pages  = get_posts(
			array(
				'order'          => 'ASC',
				'posts_per_page' => - 1,
				'post_type'      => array(
					'post',
					'page',
				),
				'post_status'    => 'publish',
			)
		);

		if ( ! empty( $pages ) ) {
			foreach ( $pages as $page ) {
				$result[] = array(
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
			$ina_multiuser_settings = get_option( '__ina_multiusers_settings' );
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
	 * @author  Deepen
	 * @return BOOL
	 */
	public function ina_check_user_role() {
		$user      = wp_get_current_user();
		$ina_roles = get_option( '__ina_multiusers_settings' );
		$result    = false;
		if ( $ina_roles ) {
			foreach ( $ina_roles as $role ) {
				if ( 1 === intval( $role['disabled_feature'] ) ) {
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
	 * @author  Deepen
	 * @since   1.6.0
	 * @return BOOL
	 */
	public function ina_check_user_role_concurrent_login() {
		$user      = wp_get_current_user();
		$ina_roles = get_option( '__ina_multiusers_settings' );
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
}
