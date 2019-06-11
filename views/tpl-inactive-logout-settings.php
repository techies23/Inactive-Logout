<?php
/**
 * Template for Settings page.
 *
 * @package inactive-logout
 */

?>

<h1><?php esc_html_e( 'Inactive User Logout Settings', 'inactive-logout' ); ?></h1>

<?php ina_helpers()->show_plugin_referrals(); ?>

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
		<?php esc_html_e( 'Role Based Timeout', 'inactive-logout' ); ?>
    </a>
    <?php do_action('ina_settings_page_tabs_after'); ?>
    <a href="?page=inactive-logout&tab=ina-support" class="nav-tab <?php echo ( 'ina-support' === $active_tab ) ? esc_attr( 'nav-tab-active' ) : ''; ?>" ><?php esc_html_e( 'Support', 'inactive-logout' ); ?></a>
</h2>