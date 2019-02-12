<?php
/**
 * Template for Settings page.
 *
 * @package inactive-logout
 */

?>

<h1><?php esc_html_e( 'Inactive User Logout Settings', 'inactive-logout' ); ?></h1>

<div id="message" class="notice notice-warning">
    <h3><?php esc_html_e( 'Like this plugin ?', 'inactive-logout' ); ?></h3>
    <p>
		<?php
		// translators: anchor tag.
		printf( esc_html__( 'Please consider giving a %s if you found this useful at wordpress.org.', 'inactive-logout' ), '<a href="https://wordpress.org/support/plugin/inactive-logout/reviews/#new-post">5 star thumbs up</a>' );
		?>
    </p>
</div>

<div class="message">
	<?php
	$message = self::get_message();
	if ( isset( $message ) && ! empty( $message ) ) {
		echo $message;
	}
	?>
</div>

<h2 class="nav-tab-wrapper">
    <a href="?page=inactive-logout&tab=ina-basic" class="nav-tab <?php echo ( 'ina-basic' === $active_tab ) ? esc_attr( 'nav-tab-active' ) : ''; ?>">
		<?php esc_html_e( 'Basic Management', 'inactive-logout' ); ?>
    </a>
    <a href="?page=inactive-logout&tab=ina-advanced" class="nav-tab <?php echo ( 'ina-advanced' === $active_tab ) ? esc_attr( 'nav-tab-active' ) : ''; ?>">
		<?php esc_html_e( 'Advanced Management', 'inactive-logout' ); ?>
    </a>
    <a href="https://deepenbajracharya.com.np/say-hello/" target="_blank" class="nav-tab"><?php esc_html_e( 'Support', 'inactive-logout' ); ?></a>
    <a href="https://deepenbajracharya.com.np/donate/" target="_blank" class="nav-tab"><?php esc_html_e( 'Donate !', 'inactive-logout' ); ?></a>
</h2>
