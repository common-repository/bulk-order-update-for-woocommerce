<?php

/***This is a Template of Plugin. Initial HTML of the Plugin***/

$loader_image_url = bouw_IMAGE_PATH.'loader.gif';

//echo $loader_image_url;

?>
<div class="bouw-container">
    <div class="bour-header">
        <h3><?php esc_html_e( 'Bulk Order Update for WooCommerce', 'bouw' ); ?></h3>
        <a class="bouw_settings" href="#"><?php esc_html_e( 'Settings', 'bouw' ); ?></a>
    </div>
    <div class="bouw-config-panel" style="display:none;">
        <label for="bouw_interval">
            <?php esc_html_e( 'Max Execution Time', 'bouw' ); ?>
        </label>
        <input type="number" name="bouw_interval" class="bouw_interval" step="500" min="500" max="5000" value="1500">
        <label for="bouw_interval_per_item">
            <?php esc_html_e( 'Items Processed Per Interval', 'bouw' ); ?>
        </label>
        <input type="number" name="bouw_interval_per_item" class="bouw_interval_per_item" step="10" min="10" max="100" value="10">
        <p class="bouw_hint"><?php esc_html_e( '( Keep the default settings, if you have a Low Configuration Server. Increase the Max Execution time, if CSV processing failed at Deafult Configuration. )', 'bouw' ); ?></p>
    </div>
    <div class="bouw_preload" style="display:none;">
        <img src="<?php echo esc_url($loader_image_url, 'bouw');?>"/>
        <p class="Progress Counter" id="anime-1">
            <span class="bouw_loader_message">
                <?php esc_html_e( 'Processing', 'bouw' ); ?>
            </span>
            <span class="bouw_pg_current">
                <?php esc_html_e( '0', 'bouw' ); ?>
            </span>
            <?php esc_html_e( '/', 'bouw' ); ?> 
            <span class="bouw_pg_total">
                <?php esc_html_e( '0', 'bouw' ); ?>  
            </span>
        </p>
    </div>
    <label for="csv_url">
        <?php esc_html_e( 'Upload Order CSV FILE (.csv)', 'bouw' ); ?>
    </label>
    <input type="text" name="csv_url" id="csv_url" class="regular-text" readonly>
    <input type="button" name="upload-btn" id="upload-btn" class="button-secondary bouw-upload-csv" value="Upload File">
    <div class="bouw-order-config">
        
    </div>
    <div class="bouw-table-preview">
        <table>
            
        </table>
    </div>
    <div class="bouw-table-action" style="display:none;">
        <?php esc_html_e( 'Change All Order Status from', 'bouw' ); ?>
        <select class="status_from">
            <?php foreach ( wc_get_order_statuses() as $key => $value) { ?>
                <option value="<?php esc_html_e( str_replace('wc-', '', $key), 'bouw' ); ?>"><?php esc_html_e( $value, 'bouw' ); ?></option>
            <?php } ?>
        </select>
            <?php esc_html_e( 'to', 'bouw' ); ?> 
        <select class="status_to">
            <?php foreach ( wc_get_order_statuses() as $key => $value) { ?>
                <option value="<?php esc_html_e( str_replace('wc-', '', $key), 'bouw' ); ?>"><?php esc_html_e( $value, 'bouw' ); ?></option>
            <?php } ?>
        </select>
        <button class="button-primary bouw-change-status">
            <?php esc_html_e( 'Change Status', 'bouw' ); ?> 
        </button>
    </div>
</div>