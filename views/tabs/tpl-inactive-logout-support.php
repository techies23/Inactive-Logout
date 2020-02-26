<?php
/**
 * Template for Basic settings page.
 *
 * @package inactive-logout
 */
?>

<div class="ina-settings-admin-wrap ina-settings-admin-support">
    <div class="ina-settings-admin-support-bg red">
        <h3>Need more features ?</h3>
        <p>Among many other features/enhancements, inactive logout pro comes with a few additional features if you feel like you need it. <a href="https://www.codemanas.com/downloads/inactive-logout-pro/">Check out the pro version here</a> to download.</p>
        <ol>
            <li>Auto browser close logout.</li>
            <li>Individual role browser close logout enable/disable option.</li>
            <li>Override Multiple Login priority</li>
            <li>Disable inactive logout for specified pages according to your need. Check this Documentation for additional post type support.</li>
            <li>Multi-User configurations ( Coming Soon )</li>
            <li>And more..</li>
        </ol>
    </div>

	<?php if ( ! in_array( 'inactive-logout-addon/inactive-logout-addon.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) { ?>
        <div class="ina-settings-admin-support-bg">
            <p>Support for this plugin is free! If you encounter any issues or have any queries please use the <a href="https://wordpress.org/support/plugin/inactive-logout" target="_blank">support forums</a> or <a href="https://deepenbajracharya.com.np/say-hello/" target="_blank">send a support mail</a>. I will reply to you at the earliest possible.</p>
            <p>If you are planning to do something creative with inactive logout, you might want to <a href="https://deepenbajracharya.com.np/hire-me/" target="_blank">hire a freelance developer</a> to assist you.</p>
        </div>
	<?php } else { ?>
        <div class="ina-settings-admin-support-bg">
            <h3>Premium Support Ticket</h3>
            <p>Create a ticket or view if your queries are already answered in <a href="https://www.codemanas.com/forums/forum/premium-plugins/inactive-logout-pro/">Support forum</a>. </p>
            <p>Check <a href="https://www.codemanas.com/downloads/inactive-logout-pro/">site</a> for recent change logs and updates.</p>
        </div>
	<?php } ?>

	<?php if ( ! in_array( 'inactive-logout-addon/inactive-logout-addon.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) { ?>
        <div class="ina-settings-admin-support-bg">
            <h3>Rate This Plugin</h3>
            <p>I take a lot of pride in our work and try our best to develop a good plugin. I really appreciate if you can spare a minute to <a href="https://wordpress.org/plugins/inactive-logout/#reviews" target="_blank">rate the plugin.</a></p>
        </div>

        <div class="ina-settings-admin-support-bg">
            <h3>Support This Plugin</h3>
            <p>I spend most of my spare time developing this plugin so you can use it for free.</p>
            <p>If you are using and benefiting from this plugin and wish to show your support, you can buy me a <a href="https://deepenbajracharya.com.np/donate/" target="_blank">coffee</a> you should know that I greatly appreciate this gesture. Every little bit helps!</p>
        </div>
	<?php } ?>

    <div class="ina-settings-admin-support-bg">
        <h3>Developer</h3>
        <p>Feel free to reach me from <a href="https://deepenbajracharya.com.np/say-hello/" target="_blank">Here</a>, if you have any questions or queries.</p>
    </div>

</div>
