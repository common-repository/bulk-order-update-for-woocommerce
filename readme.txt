=== Bulk Order Update for WooCommerce ===
Contributors: sayantandas20
Tags: csv, ajax, woocommerce, order, bulk-update, large-csv, split response
Requires at least: 5.0
Requires PHP: 5.6
Tested up to: 6.2.2
Stable tag: 1.6
License: GPLv3 ONLY
License URI: https://www.gnu.org/licenses/gpl-3.0.html

== Description ==

Bulk Modify Woocommerce orders via a CSV file. This plugin can be used for large number of orders. A very simple lightweight tool for the people who manages their inventories offline.

== Installation ==

Install Bulk Order Update for WooCommerce like you would install any other WordPress plugin.

Dashboard Method:

1. Login to your WordPress admin and go to Plugins -> Add New
2. Type "Bulk Order Update for WooCommerce" in the search bar and select this plugin
3. Click "Install", and then "Activate Plugin"


Upload Method:

1. Unzip the plugin and upload the "bulk-order-update-for-woocommerce" folder to your 'wp-content/plugins' directory
2. Activate the plugin through the Plugins menu in WordPress

== Configuration ==

By default this Plugin can process 10 items per second from the CSV. You can increase the Max Exectution Time from the Settings if the CSV Data read process is Failing. 

You can also increase Items Processed Per Interval upto 100 (If your site is running on a High Configuration Server). [Please Visit our Support Forum for more information](https://wordpress.org/support/plugin/bulk-order-update-for-woocommerce/)

== Frequently Asked Questions ==


= What is the maximum size of CSV file we can upload =

There is no limit to it. 10mb to 10gb, all works fine. Just do not refresh the page until all the data from the csv is processed.

= Is this plugin create load on Server =

No. It parses the CSV data line by line. Currently it is set to 10 items per seconds.

= How much does Bulk Order Update for WooCommerce cost? =

It's a Free plugin. There is no pro version available for this. Please use this plugin and let us know about your valuable suggestions for improvemnt of this plugin. If you face any error, feel free to mention them on the support forum. We will surely look into it.

== Screenshots ==

1. Select Bulk Order Update under Woocommerce Admin Menu.
2. Click on the Upload File Button and select the CSV file (No other file format will be allowed). If you have a huge CSV file please check your WordPress upload limit before the upload.
3. Once the CSV file is selected, verifed it's data by selecting the column from CSV containing Woocommerce order ID. Please wait until the CSV is processed. Do not refresh the Page.
4. Once all the CSV data loaded, select the Order status dropdown under the Table to modify order status. 
5. On the first dropdown you have to select the Current Status, and on the second one You have to select your desired order status.
6. Click on Settings to increase the Max Exectution Time and Items Processed Per Interval(If your site is running on a High Configuration Server).

== Changelog ==

= 1.1 =
* Release of the plugin

= 1.2 =
* Fixed Plugin Name in readme.txt

= 1.3 =
* Plugin Page Description Updated
* Plugin Logo Updated


= 1.4 =
* Fixed Preloader Message
* Added Settings for Max Execution Time & Items per Second


= 1.5 =
* Changed Plugin Logo and Banner
* Minor Bug Fixes for WordPress 5.9.3
* wp_enqueue_media function added


= 1.6 =
* Added compatibility for Latest WP Version
