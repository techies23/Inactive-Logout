<?php
/**
 * Class for defining admin pointers
 *
 * @author Deepen.
 * @created_on 6/13/19
 */

// Don't load directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class Inactive_Logout_Pointers {
	public $valid;

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'load_intro_tour' ) );
		add_action( 'wp_ajax_ina_disable_tour_mode', array( $this, 'disable_tour' ) );
	}

	/**
	 * Give the user an introductory tour to your plugin
	 * @return array | mixed
	 */
	public function load_intro_tour() {
		// Don't run on WP < 3.3. Admin pointers were only introduced in WP 3.3
		if ( get_bloginfo( 'version' ) < '3.3' ) {
			return false;
		}

		//Do a check to see whether your user wants to take the tour. You can check
		//a custom plugin setting here like this:
		if ( ! empty( get_option( 'ina_tour_dismissed' ) ) && "yes" === get_option( 'ina_tour_dismissed' ) ) {
			return false;
		}

		//Generate the tour messages
		$pointers = $this->generate_tour_content();

		// No pointers? Then we stop.
		if ( ! $pointers || ! is_array( $pointers ) ) {
			return false;
		}

		wp_enqueue_style( 'wp-pointer' );//Needed to style the pointers.
		wp_enqueue_script( 'wp-pointer' );//Has the actual pointer logic
		wp_enqueue_script( 'inactive-pointer-admin-js', INACTIVE_LOGOUT_ASSETS_URL . 'admin/admin-pointers.js', array( 'jquery' ), '1.0.0' );

		$tour_pointer_messages['inactive_logout_intro_tour'] = $pointers;
		wp_localize_script( 'inactive-pointer-admin-js', 'inactive_pointer', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'pointers' => $tour_pointer_messages
		) );


		$this->valid = $pointers;
	}

	/**
	 * The tour content for the different screens
	 */

	private function generate_tour_content() {
		//The content is entered into the array based on when it should display since
		//it'll be displayed sequentially i.e. content at $p[0] will come first, then $p[n+1]
		$p['ina_pointer_dialog'] = array(
			"target"  => "#menu-settings",
			"screen"  => 0,
			"options" => array(
				"content"  => sprintf( "<span><h3>%s</h3><p>%s</p></span>", __( "Configure Inactive Logout", "inactive-logout" ), __( "Configure the idle timeout, user messages and other plugin and inactive sessions settings from <a href=\"options-general.php?page=inactive-logout\">Here</a>", "inactive-logout" ) ),
				"position" => array( 'edge' => 'left', 'align' => 'left' )
			)
		);

		return $p;
	}

	/**
	 * Disable tour mode
	 */
	public function disable_tour() {
		update_option( "ina_tour_dismissed", "yes" );
		echo json_encode( 1 );
		die();
	}
}

new Inactive_Logout_Pointers();
