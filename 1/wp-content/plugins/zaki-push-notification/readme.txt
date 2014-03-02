=== Zaki Push Notification ===
Contributors: r.conte
Donate link: http://www.zaki.it
Tags: posts, apple, push notification, apn, iphone, ipad
Requires at least: 3.3
Tested up to: 3.7.1
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add the Apple Push Notification Service (APNs) at your site. 

== Description ==

This plugin implements the Apple Push Notification Service (APNs) and allows you to send notifications from your site to all devices that have installed your app. A button is added next to the button for publishing the post on the edit page to do this.

All registration/deletion requests must be sent to a specific URL of the website and must be composed by the UDID code followed by the device token (Look at the documentation page of plugin after installation).

In the settings page you can also upload your PEM certificate file and set all other information such as PEM SSL password, SSL server url and port. The PEM file will be stored in a protected folder for security reason.

== Screenshots ==

1. The button for notifications appears on the edit page
2. Settings page

== Installation ==

1. Unzip and upload the plugin in your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Drag & Drop the widget in your sidebar and customize your options

== Frequently asked questions ==

= Will be provided for new features? =
Yes! We are already working on improvements such as the setting of auto notification for new articles.

== Changelog ==

= 1.1 =
* (Bug fix) Function that add plugin rewrite rules moved to admin_init hook with conditional rewrite flush to avoid custom post type pages to display 404 error message

= 1.0 =
* First release of the plugin

