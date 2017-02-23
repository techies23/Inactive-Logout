<h1><?php _e("Inactive User Logout Settings", "ina-logout"); ?></h1>
<?php if( $saved ) { ?>
<div id="message" class="updated notice is-dismissible"><p><?php _e("Updated !", "ina-logout"); ?></p><button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php _e("Dismiss this notice.", "ina-logout"); ?></span></button></div>
<?php } ?>

<h2 class="nav-tab-wrapper">
	<a href="?page=inactive-logout&tab=ina-basic" class="nav-tab <?php echo $active_tab == 'ina-basic' ? 'nav-tab-active' : ''; ?>"><?php _e("Basic Management", "ina-logout"); ?></a>
	<a href="?page=inactive-logout&tab=ina-advanced" class="nav-tab <?php echo $active_tab == 'ina-advanced' ? 'nav-tab-active' : ''; ?>"><?php _e("Advanced Management", "ina-logout"); ?> <span style="color:red;">(Beta)</span></a>
  <a href="https://deepenbajracharya.com.np/say-hello/" class="nav-tab"><?php _e('Support', 'ina-logout'); ?></a>
  <a href="https://deepenbajracharya.com.np/donate-via-paypal" class="nav-tab"><?php _e('Donate !', 'ina-logout'); ?></a>
</h2>
