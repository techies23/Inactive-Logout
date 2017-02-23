<?php if( isset($ina_logout_time) ) { ?>
<meta name="ina_timeout" content="<?php echo $ina_logout_time; ?>"/>
<?php } ?>
<?php if( $idle_disable_countdown == 1 ) { ?>
<meta name="ina_disable_countdown" content="<?php echo '1'; ?>"/>
<?php } ?>
<?php if( $ina_warn_message_enabled == 1 ) { ?>
<meta name="ina_warn_message_enabled" content="<?php echo '1'; ?>"/>
<?php } ?>