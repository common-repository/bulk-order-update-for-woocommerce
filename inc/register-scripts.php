<?php
/*Register all CSS and JS to the plugin*/

function bouw_admin_scripts() {
       wp_enqueue_media();
       wp_register_style( 'bouw_css', plugin_dir_url( __DIR__ ) . 'assets/css/main.css', false, '1.0.0' );
       wp_enqueue_style( 'bouw_css' );

       wp_register_script( 'bouw_js', plugin_dir_url( __DIR__ ) . 'assets/js/main.js', array('jquery'), '1.0' );
       wp_localize_script( 'bouw_js', 'wpAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));        
	   wp_enqueue_script( 'jquery' );
	   wp_enqueue_script( 'bouw_js' );
}
add_action( 'admin_enqueue_scripts', 'bouw_admin_scripts' );
