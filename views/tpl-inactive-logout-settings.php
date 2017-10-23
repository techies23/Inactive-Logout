<h1><?php _e("Inactive User Logout Settings", "inactive-logout"); ?></h1>

<!-- <div id="message" class="updated notice is-dismissible"><p><?php _e("Updated !", "inactive-logout"); ?></p><button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php _e("Dismiss this notice.", "inactive-logout"); ?></span></button></div> -->

<div id="message" class="notice notice-warning is-dismissible"><h3><?php _e('Like this plugin ?', 'inactive-logout'); ?></h3><p><?php printf( __("Please consider giving a %s if you found this useful at wordpress.org.", "inactive-logout"), '<a href="https://wordpress.org/support/plugin/inactive-logout/reviews/#new-post">5 star thumbs up</a>'); ?></p><button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php _e("Dismiss this notice.", "inactive-logout"); ?></span></button></div>

<h2 class="nav-tab-wrapper">
	<a href="?page=inactive-logout&tab=ina-basic" class="nav-tab <?php echo $active_tab == 'ina-basic' ? 'nav-tab-active' : ''; ?>"><?php _e("Basic Management", "inactive-logout"); ?></a>
	<a href="?page=inactive-logout&tab=ina-advanced" class="nav-tab <?php echo $active_tab == 'ina-advanced' ? 'nav-tab-active' : ''; ?>"><?php _e("Advanced Management", "inactive-logout"); ?></a>
  <a href="https://deepenbajracharya.com.np/say-hello/" target="_blank" class="nav-tab"><?php _e('Support', 'inactive-logout'); ?></a>
  <a href="https://github.com/techies23/Inactive-Logout" target="_blank" class="nav-tab"><?php _e('Contribute !', 'inactive-logout'); ?></a>
</h2>
