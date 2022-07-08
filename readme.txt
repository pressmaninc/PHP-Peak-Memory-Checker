===  PHP Peak Memory Checker ===
Contributors: pressmaninc, hiroshisekiguchi, kazunao, nananoguchi
Tags: pressman, peak, memory, alert, notification, mail, WP10
Requires at least: 5.2.3
Tested up to: 6.0
Stable tag: 1.0
Requires PHP: 7.3
License: GNU GPL v2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

This plugin checks PHP memory usage and sends an email to the administrator if the maximum memory usage exceeds the threshold.

== Description ==
This plugin checks PHP memory usage and sends an email to the administrator if the maximum memory usage exceeds the threshold.
This email will include the memory usage, $_SERVER, and $_REQUEST values.
When used in conjunction with the External Notification plugin, the email can be sent as a notification to Slack.

== For developer ==
1. The default value for the threshold is 32 MB, but can be set to any value. Please define WP10_MEMORY_ALERT_THRESHOLD in megabytes in wp-config.php file.
2. The plugin has a filter hook called "cpmp_delete_things_list". This hook can be used to delete any content from the information retrieved by $_REQUEST.

== Installation ==
1. Upload the plugin package to the plugins directory.
2. Activate the plugin through the \'Plugins\' menu in WordPress.

== Changelog ==
= 1.0 =
* first version.

= 1.0.1 =
* fix code.