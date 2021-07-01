<?php
$time                     = ina_helpers()->get_option( '__ina_logout_time' );
$countdown_enable         = ina_helpers()->get_option( '__ina_disable_countdown' );
$ina_warn_message_enabled = ina_helpers()->get_option( '__ina_warn_message_enabled' );
$ina_concurrent           = ina_helpers()->get_option( '__ina_concurrent_login' );
$ina_enable_redirect      = ina_helpers()->get_option( '__ina_enable_redirect' );
?>
<div class="ina-debugger-section">
    <ul>
        <li><span class="coutdown-timer">Waiting for Inactivity...</span></li>
        <li><strong>Logout Timeout Duration:</strong> <span class="ina-debugger-color"><?php echo ina_helpers()->ina_convert_to_minutes( $time ); ?></span></li>
        <li><strong>Countdown Disabled ?</strong> <span class="ina-debugger-color"><?php echo ! empty( $countdown_enable ) ? 'Yes' : 'No'; ?></span></li>
        <li><strong>Warning only ?</strong> <span class="ina-debugger-color"><?php echo ! empty( $ina_warn_message_enabled ) ? 'Yes' : 'No'; ?></span></li>
        <li><strong>Concurrent Login Enabled ?</strong> <span class="ina-debugger-color"><?php echo ! empty( $ina_concurrent ) ? 'Yes' : 'No'; ?></span></li>
        <li><strong>Redirect Enabled ?</strong> <span class="ina-debugger-color"><?php echo ! empty( $ina_enable_redirect ) ? 'Yes' : 'No'; ?></span></li>
    </ul>
</div>