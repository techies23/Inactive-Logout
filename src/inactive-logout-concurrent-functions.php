<?php
//Not Permission to agree more or less then given
if( !defined('ABSPATH') ) {
	die( '-1' );
}

/**
 * Core Functions for Concurrent Logins
 *
 * Derived from Prevent Concurrent Logins by Frankie Jarrett
 *
 * @since  1.1.0
 */
class Inactive__ConcurrentLogins_functions {

	public function __construct() {
		add_action( 'init', array( $this, 'ina_concurrent_logins' ) );
	}

	/**
	 * Detect if the current user has multiple sessions
	 *
	 * @since  1.1.0
	 * @return bool
	 */
	public function ina_user_has_multiple_sessions() {
		return ( is_user_logged_in() && count( wp_get_all_sessions() ) > 1 );
	}

	/**
	* Get the user's current session array
	*
	* @return array
	*/
	public function ina_get_current_session() {
		$sessions = WP_Session_Tokens::get_instance( get_current_user_id() );
		return $sessions->get( wp_get_session_token() );
	}

	/**
	* Only allow one session per user
	*
	* If the current user's session has been taken over by a newer
	* session then we will destroy their session automattically and
	* they will have to login again to continue.
	*
	* @action init
	*/
	public function ina_concurrent_logins() {
		if ( ! $this->ina_user_has_multiple_sessions() ) {
			return;
		}

		$user_id = get_current_user_id();

		/**
		* Filter to allow certain users to have concurrent sessions when necessary
		*
		* @param bool $prevent
		* @param int  $user_id ID of the current user
		* @return bool
		*/
		if ( false === (bool) apply_filters( 'ina_allow_multiple_sessions', true, $user_id ) ) {
			return;
		}

		//Finding maximum value of all sessions available
		$newest = max( wp_list_pluck( wp_get_all_sessions(), 'login' ) );
		$session = $this->ina_get_current_session();
		if ( $session['login'] === $newest ) {
			wp_destroy_other_sessions();
		} else {
			wp_destroy_current_session();
		}
	}

	/**
	* Get all users with active sessions
	*
	* @return WP_User_Query
	*/
	protected static function ina_get_users_with_sessions() {
		$args = array(
		'number'     => '', // All users
		'blog_id'    => is_network_admin() ? 0 : get_current_blog_id(),
		'fields'     => array( 'ID' ), // Only the ID field is needed
		'meta_query' => array(
			array(
				'key'     => 'session_tokens',
				'compare' => 'EXISTS',
				),
			),
		);

		$users = new WP_User_Query( $args );
		return $users;
	}

	/**
	* Destroy old sessions for all users
	*
	* This function is meant to run on activation only so that old
	* sessions can be cleaned up immediately rather than waiting for
	* every user to login again.
	*/
	public static function ina_destroy_all_old_sessions() {
		$users = self::ina_get_users_with_sessions()->get_results();

		foreach ( $users as $user ) {
			$sessions = get_user_meta( $user->ID, 'session_tokens', true );

			// Move along if this user only has one session
			if ( 1 === count( $sessions ) ) {
				continue;
			}

			// Extract the login timestamps from all sessions
			$logins = array_values( wp_list_pluck( $sessions, 'login' ) );

			// Sort by login timestamp DESC
			array_multisort( $logins, SORT_DESC, $sessions );

			// Get the newest (top-most) session
			$newest = array_slice( $sessions, 0, 1 );

			// Keep only the newest session
			update_user_meta( $user->ID, 'session_tokens', $newest );
		}
	}
}

new Inactive__ConcurrentLogins_functions();