<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class with a few helpers
 */
class Inactive__logout__Helpers {

	protected static $instance;

	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			$className      = __CLASS__;
			self::$instance = new $className;
		}
		return self::$instance;
	}

	/**
	* Define constant if not already set.
	*
	* @param  string $name
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
		$minutes = floor($value / 60);
		return $minutes . ' ' . __( "Minute(s)", "ina-logout");
	}

	public function ina_reload() {
		?>
		<script type="text/javascript">location.reload();</script>
		<?php
	}

}