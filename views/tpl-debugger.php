<?php
$time                          = ina_helpers()->get_option( '__ina_logout_time' );
$countdown_enable              = ina_helpers()->get_option( '__ina_disable_countdown' );
$ina_warn_message_enabled      = ina_helpers()->get_option( '__ina_warn_message_enabled' );
$ina_concurrent                = ina_helpers()->get_option( '__ina_concurrent_login' );
$ina_enable_redirect           = ina_helpers()->get_option( '__ina_enable_redirect' );
$ina_multiuser_timeout_enabled = ina_helpers()->get_option( '__ina_enable_timeout_multiusers' );

if ( ! ina_helpers()->ina_check_user_role() ) {
	?>
    <div class="ina-debugger-section">
        <div class="ina-debugger-section-btn">
            <span class="ina-debugger-section-btn-close" data-state="close"></span>
        </div>
        <ul>
            <li><span class="coutdown-timer"><?php _e( 'Waiting for Inactivity', 'inactive-logout' ); ?>...</span></li>
            <li><strong><?php _e( 'Logout Timeout Duration', 'inactive-logout' ); ?>:</strong> <span class="ina-debugger-color"><?php echo ina_helpers()->ina_convert_to_minutes( $time ); ?></span></li>
            <li><strong><?php _e( 'Countdown Disabled', 'inactive-logout' ); ?> ?</strong> <span class="ina-debugger-color"><?php echo ! empty( $countdown_enable ) ? 'Yes' : 'No'; ?></span></li>
            <li><strong><?php _e( 'Warning only', 'inactive-logout' ); ?> ?</strong> <span class="ina-debugger-color"><?php echo ! empty( $ina_warn_message_enabled ) ? 'Yes' : 'No'; ?></span></li>
            <li><strong><?php _e( 'Concurrent Login Enabled', 'inactive-logout' ); ?> ?</strong> <span class="ina-debugger-color"><?php echo ! empty( $ina_concurrent ) ? 'Yes' : 'No'; ?></span></li>
            <li><strong><?php _e( 'Redirect Enabled', 'inactive-logout' ); ?> ?</strong> <span class="ina-debugger-color"><?php echo ! empty( $ina_enable_redirect ) ? 'Yes' : 'No'; ?></span></li>
            <li><strong><?php _e( 'Role Based Enabled', 'inactive-logout' ); ?> ?</strong> <span class="ina-debugger-color"><?php echo ! empty( $ina_multiuser_timeout_enabled ) ? 'Yes' : 'No'; ?></span></li>
        </ul>
    </div>
<?php } ?>