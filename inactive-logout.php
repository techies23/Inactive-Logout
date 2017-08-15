<?php
/**
* @link              http://www.deepenbajracharya.com.np
* @since             1.0.0
* @package           Inactive Logout
*
* Plugin Name:       Inactive Logout
* Plugin URI:        https://www.deepenbajracharya.com.np
* Description:       Inactive logout provides functionality to log out any idle users defined specified time showing a message. Works for frontend as well.
* Version:           1.6.0
* Author:            Deepen Bajracharya
* Author URI:        https://www.deepenbajracharya.com.np
* License:           GPL-2.0+
* License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
* Text Domain:       ina-logout
* Domain Path:       /lang
**/

//Not Permission to agree more or less then given
if( !defined('ABSPATH') ) {
	die( '-1' );
}

// the main plugin class
require_once dirname( __FILE__ ) . '/src/inactive-logout-main.php';

add_action( 'plugins_loaded', array( 'Inactive__Logout_Main', 'instance' ) );
register_activation_hook( __FILE__, array( 'Inactive__Logout_Main', 'ina_activate' ) );
register_deactivation_hook( __FILE__, array( 'Inactive__Logout_Main', 'ina_deactivate' ) );