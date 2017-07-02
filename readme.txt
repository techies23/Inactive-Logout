=== Inactive Logout ===
Contributors: j__3rk
Tags: logout, inactive user, idle, idle logout, idle user, auto logout, autologout, inactive, inactive, automatic logout, multisite autologout, multisite inactive logout, multisite inactive user, multisite, concurrent logout, multiple sessions, multiple user logout, concurrent login
Donate link: https://deepenbajracharya.com.np/say-hello/
Requires at least: 4.5.2
Tested up to: 4.8
Stable tag: 1.5.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Logs out users within defined time when inactive. Modify to show only wake up message and not log out as well. Supported for multisites as well.

== Description ==

Make your WP account secure from snoopers, friends to protect your data by assuring auto log out system within a certain defined time. This will help you keep safe from any users using same machine to access your account in case you are away and forgot to logout and kept your site logged open.

Simple but works efficiently. Nothing much to explain on plugin use. As its simple to use. You can find inactive settings under settings menu in admin dashboard.

**Please check [changelog](https://wordpress.org/plugins/inactive-logout/#developers "Change Log") to see what is added from version 1.3.0**

**Some Feature Highlights**.

1. Change idle timeout time.
2. Count down of 10 seconds before actual logout. You can remove this feature if you dont want it.
3. Add only **Wake Up!** message where user will not logout but instead a wakeup message will be shown upon inactive.
4. Custom Popup Message.
5. Choose to use concurrent logout functionality derived from [prevent concurrent logins](https://wordpress.org/plugins/prevent-concurrent-logins/ "Prevent Concurrent Logins") by Frankie Jarrett. Thumbs up here too !
6. Redirect to a Different Page instead of Popup box. Create a page such as timeout page and add your content there by creating a blank template or style it as you wish according to your theme.
7. Multiple User Role Confiurations for individual timeout and redirects.
8. Clean UI
9. Simple to use
10. Multi browser tab support: Means that logout will not happen even if the user has multiple browser tabs opened and is active in certain browser tab.
11. Logs out the session even after the browser tab is closed.

In order to style dialog boxes you can use css classes. Also, works in **frontend view as well**.

Lemme know if there are any bugs and problems or enhancements you want to make..

**See the [Inactive Logout](https://deepenbajracharya.com.np/wp-inactive-logout/ "Inactive Logout") homepage for further information. Contact Developer for those who need to write plugins.**

**There's a [GIT repository](https://github.com/techies23/Inactive-Logout.git "Github Inactive Repository") too if you want to contribute a patch. Please check issues. Pull requests are welcomed.**

**Please consider giving a [5 star thumbs up](https://wordpress.org/support/plugin/inactive-logout/reviews/#new-post "5 star thumbs up") if you found this useful.**

== Installation ==

Upload the plugin to your blog, Activate it, Load...and You're done!

== Frequently Asked Questions ==

= Plugin Conflicts =

Slim Stat Analytics: Users using "Slimstat Analytics" plugin version upto 4.6.2 might find conflict issue with colorpicker javascript library. This conflict was identified by [psn](https://wordpress.org/support/users/psn/ "PSN") and has been fixed in later versions of slim stat analytics.

= Popup Modal Customization HTML Render Elements =

* For Default popup customization: [Code](https://gist.github.com/techies23/e9b54467b05f25f189ed5ff52375ef41 "Default popup code")
* For Wakeup popup customization: [Code](https://gist.github.com/techies23/546b9a85eda645207704cb9cf1cf8a9a "Wakeup popup code")

= Old users upgrading to version 1.4.1 =

Users might face logout after activating or deactivating the plugin. Try to login again. If this does not work out then download the latest plugin by deleting the old version.

== Screenshots ==

1. Showing Inactive Logout Settings Page.
2. Wakeup functionality message box.
3. Session going to logout if continue is clicked then session will not end.
4. Multi User Role Screen

== Changelog ==

= 1.5.0 =
* Added External Page Redirect. Select from "Redirect Page" and choose option "External Page redirect". Available only for Basic settings.
* Major Bug Fixes

= 1.4.7 =
* WordPress 4.8 compatible

= 1.4.4 - 1.4.5 =
* Removed Functionality: Removed auto logout added in v1.4.1 - 1.4.3 due to logout bug.
* Minor Bug Fixes

= 1.4.3 =
* Bug Fix: Fixed logout caused when plugin is activated.

= 1.4.2 =
* Bug Fix: Fixed logout when plugin is deactivated.

= 1.4.1 =
* Added: Logout session even after the browser is closed.

= 1.4.0 =
* Change: Added constant login functionality for all browser tabs which means even if the user has multiple browser tabs opened. Until the user is active plugin will not show any popups or logout the user. The timeout will only show in the last active tab window.

= 1.3.5 =
* Updated: Updated Sweedish translation.
* Change: Small fix regarding php version compatibility.
* Removed: Beta Version for advanced management

= 1.3.4 =
* Security: Fixed a non-security though a security issue. Where a variable named system is changed because virustotal was showing it was a threat.

= 1.3.3 =
* Updated: Spanish translation. Compatible to version 1.3. Thanx to Miguel Arroyo.

= 1.3.2 =
* Updated: German translation. Compatible to version 1.3 Thanks to Roland Dietz

= 1.3.1 =
* Updated: Swedish translation. Compatible to version 1.3 Thanks to @nijen

= 1.3.0 =
* Added: Basic and Advanced configuration features
* Minor Bug Fixes
* Added: Multi Role based configuration
* Added: Multi Role based redirection
* Added: Multi Role based feature disable
* Added: Multi Role based timeout limit
* Added: Tab Layout for settings section

= 1.2.1 =
* Changes: Classes changes in order to avoid any conflict with JS issues.
* Added: Spanish translation. Thanx to Miguel Arroyo.
* Updated: Swedish translation. Thanx to Björn Granberg.
* Minor bug fixes.

= 1.2.0 =
* Feature: Added Redirection to different page after logout functionality.
* Bug: Minor bug fixes.

= 1.1.3 =
* Bug: Activation Bug Fix

= 1.1.2 =
* Corrected Swedish Translation. Thanks to @nijen

= 1.1.1 =
* Corrected German Translation. Thanx to Roland Dietz.
* Corrected Localization String in Helper Class.

= 1.1.0 =
* Added Concurrent Login Functionality referencing from prevent concurrent logins by Frankie Jarrett
* Fixed Translation Errors
* Added Swedish Translation thanks to @nijen
* Added Popup Solid Background Feature
* Few Bug Fixes

= 1.0 =
* Initial Release

== Upgrade Notice ==

= 1.5.0 =
* Major improvements and fix updates, verify change log for upgrade.
= 1.4.5 =
Please read FAQ section for old users.
= 1.4.3 =
Please read FAQ section for old users.
= 1.4.0 =
Please upgrade to get new feature.
= 1.3.0 =
Please upgrade to get latest features.
= 1.2.0 =
Added Redirect to Custom Page functionality.
= 1.1.3 =
Crucial Upgrade. Contains fix for activation Error. Please upgrade.