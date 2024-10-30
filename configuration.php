<?php
define( 'bouw_PATH', plugin_dir_path( __FILE__ ).'inc/' );
define( 'bouw_IMAGE_PATH',   plugin_dir_url(  __FILE__  ).'images/' );
 
/**
 * Plugin Name
 *
 * @package           bouw
 * @author            Sayantan Das
 * @copyright         2021 Sayantan Das
 * @license           GPL-3.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Bulk Order Update for WooCommerce
 * Plugin URI:        https://wordpress.org/plugins/bulk-order-update-for-woocommerce/
 * Description:       Bulk Modify Woocommerce orders via a CSV or Excel file. Can be user for large number of orders.
 * Version:           1.6
 * Requires at least: 5.0
 * Requires PHP:      5.6
 * Author:            Sayantan Das
 * Author URI:        https://profiles.wordpress.org/sayantandas20
 * Text Domain:       bulk-order-update-for-woocommerce
 * License:           GPLv3 ONLY
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Update URI:        https://wordpress.org/plugins/bulk-order-update-for-woocommerce/
 */



function bouw_activate() { 
	if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) and current_user_can( 'activate_plugins' ) ) {
        // Stop activation redirect and show error
        wp_die('Sorry, but this plugin requires the Woocommerce to be installed and active. <br><a href="' . admin_url( 'plugins.php' ) . '">&laquo; Return to Plugins</a>');
    } else {
    	// Trigger our function that registers the custom post type plugin.
	    bouw_activate_init(); 
	    // Clear the permalinks after the post type has been registered.
	    flush_rewrite_rules(); 
    }

}
register_activation_hook( __FILE__, 'bouw_activate' );


function bouw_activate_init() {
	include_once( bouw_PATH.'register-scripts.php');
	include_once( bouw_PATH.'plugin-html.php');
}
add_action( 'init', 'bouw_activate_init' );