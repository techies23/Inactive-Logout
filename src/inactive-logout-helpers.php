<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class with a few helpers
 */
class Inactive_Logout_Helpers {

	protected static $instance;

	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new static;
		}
		return self::$instance;
	}

	/**
	 * Define constant if not already set.
	 *
	 * @param  string      $name
	 * @param  string|bool $value
	 * @since  2.0.0
	 *
	 * @author  Deepen Bajracharya
	 */
	public function ina_define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	public function ina_convertToMinutes( $value ) {
		$minutes = floor( $value / 60 );
		return $minutes . ' ' .esc_html__( 'Minute(s)', 'inactive-logout' );
	}

	public function ina_reload() {
		?>
		<script type="text/javascript">location.reload();</script>
		<?php
	}

	public function ina_get_all_roles() {
		$roles = get_editable_roles();
		foreach ( $roles as $role => $role_name ) {
			$result[ $role ] = $role_name['name'];
		}

		return $result;
	}

	public function ina_check_role_enabledfor_multiuser( $role = null ) {
		$selected = false;
		if ( ! empty( $role ) ) {
			$ina_multiuser_settings = get_option( '__ina_multiusers_settings' );
			if ( ! empty( $ina_multiuser_settings ) ) {
				foreach ( $ina_multiuser_settings as $ina_multiuser_setting ) {
					if ( in_array( $role, $ina_multiuser_setting ) ) {
						$selected = true;
					}
				}
			}

			return $selected;
		}
	}

	/**
	 * Check to disable the Inactive for certain user role
	 *
	 * @author  Deepen
	 * @return BOOL
	 */
	public function ina_check_user_role() {
		$user = wp_get_current_user();
		$ina_roles = get_option( '__ina_multiusers_settings' );
		$result = false;
		if ( $ina_roles ) {
			foreach ( $ina_roles as $role ) {
				if ( $role['disabled_feature'] == 1 ) {
					if ( in_array( $role['role'], (array) $user->roles ) ) {
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
	 * @since  1.6.0
	 * @return BOOL
	 */
	public function ina_check_user_role_concurrent_login() {
		$user = wp_get_current_user();
		$ina_roles = get_option( '__ina_multiusers_settings' );
		$result = false;
		if ( $ina_roles ) {
			foreach ( $ina_roles as $role ) {
				if ( ! empty( $role['disabled_concurrent_login'] ) && $role['disabled_concurrent_login'] == 1 ) {
					if ( in_array( $role['role'], (array) $user->roles ) ) {
						$result = true;
					}
				}
			}
		}

		return $result;
	}
}
