<?php
/*This plugin is dedicated to Tony Stark. Love you 3000.*/

function bouw_admin_submenu_page() {
    add_submenu_page( 
        'woocommerce', 
        'Bulk Order Update for WooCommerce', 
        'Bulk Order Update', 
        'manage_options', 
        'bulk-order-update-for-woocommerce', 
        'bouw_callback_init' 
    ); 
}
add_action('admin_menu', 'bouw_admin_submenu_page',99);

function bouw_callback_init() {
    include_once( bouw_PATH.'loadview.php');
}



function bouw_fetch_csv_data() {
    if(isset($_POST['csv_url']) && !empty($_POST['csv_url'])){
        $csv_file_path = esc_url_raw($_POST['csv_url']);
    } else {
        $csv_file_path = "";
        $html = '<p>Please Upload a CSV File</p>';
    }

    if(!validate_file( $csv_file_path )){
        $html = '<label for="select_order_field">Select the Column Containing Order ID :  </label>';
        $html .= '<select class="select_order_field" id="select_order_field" name="select_order_field">';
        $f = fopen($csv_file_path, "r");
        $line = fgetcsv($f);
        //var_dump($line);
        $html .= '<option value="">Select Order ID</option>';
        foreach ($line  as $key => $value) {
            $html .= "<option value='".htmlspecialchars($key)."'>" .htmlspecialchars($key).'. '. htmlspecialchars($value) . "</option>";
        }
        $html .= '</select>';
    } else {
        $html = '<p>File Does not Exist. Please Reupload again.</p>';
    }
    echo json_encode(array('html'=> $html ));
    exit();
}
// creating Ajax call for WordPress
add_action('wp_ajax_nopriv_bouw_fetch_csv_data', 'bouw_fetch_csv_data');
add_action('wp_ajax_bouw_fetch_csv_data', 'bouw_fetch_csv_data');



function bouw_generate_csv_table() {
    if(isset($_POST['csv_url']) && !empty($_POST['csv_url'])){
        $csv_file_path = esc_url_raw($_POST['csv_url']);
    } else {
        $csv_file_path = "";
        $html = '<p>Please Upload a CSV File</p>';
    }
    $column_key = isset($_POST['column_key']) ? sanitize_key($_POST['column_key']) : '';
    $column_key = intval($column_key);
    $interval = isset($_POST['interval']) ? sanitize_key(intval($_POST['interval'])) : '';
    $per_item = isset($_POST['peritem']) ? sanitize_key(intval($_POST['peritem'])) : '';
    $mycsvfile = array();
    $html="";

    if($interval===1){
        $start_count = $interval;
        $end_count = $per_item;
    } else { 
        $end_count = $per_item*$interval;
        $start_count = $end_count-$per_item+1;
    }

    if (($handle = fopen($csv_file_path, "r")) !== FALSE) {
        $total = count(file($csv_file_path));
        //$total = 15;
        while (($data = fgetcsv($handle, 100000, ",")) !== FALSE) {
            $mycsvfile[] = $data;
        }
        fclose($handle);
    } else {
        $status = 404;
        $msg = 'CSV File path is not Valid or File is Corrupted. Please Try Again';
        $html.='<tr id="error_column"><td colspan="5">'.$msg.'</td></tr>';
        echo json_encode(array('status'=>$status,'msg'=>$msg,'html'=>$html));
        die();
    }
    
    for($i=$start_count; $i<=$end_count; $i++) {
        if($i>$total){
            $status = 404;
            $msg = 'Processing CSV Data Completed';
            $html.='';
            break; 
        } else {
            $status = 200;
            $msg = 'CSV Data Read in Process';
            $order_id = $mycsvfile[$i][$column_key];
            $html .= bouw_generate_table_html($order_id,$i);
        }

    }
    if(!empty($html)){
        echo json_encode(array('status'=>$status,'msg'=>$msg,'html'=>$html,'current'=>$end_count,'total'=>$total));
    } else {
        $status = 404;
        if($interval<=1){
            $msg = 'No matching Order ID. Please Recheck the Column Selected/CSV Uploaded';
            $html.='<tr id="error_column"><td colspan="5">'.$msg.'</td></tr>'; 
        } else {
            $msg = 'All Orders have been Loaded. Please Do not Refresh the Page';
            $html.='<tr id="loaded_column"><td colspan="5"><b>'.$msg.'</b></td></tr>'; 
        }
        
        echo json_encode(array('status'=>$status,'msg'=>$msg,'html'=>$html,'current'=>$end_count,'total'=>$total));
    }

    
    exit();
}
// creating Ajax call for WordPress
add_action('wp_ajax_nopriv_bouw_generate_csv_table', 'bouw_generate_csv_table');
add_action('wp_ajax_bouw_generate_csv_table', 'bouw_generate_csv_table');

function bouw_generate_table_html($order_id,$i) {

    $table_data = '';
    $order_id = htmlspecialchars($order_id);
    $order = wc_get_order( $order_id );
    
    if(!empty($order)) {

        $order_data = $order->get_data();
        $order_date_created = $order_data['date_created'];
        $order_date_created = date_format($order_date_created,"Y/m/d");
        $customer_name="";
        if(isset($order_data['billing']['first_name'])){
            $first_name = $order_data['billing']['first_name'];
        } else {
            $first_name = '';
        }
        if(isset($order_data['billing']['first_name'])){
            $last_name = $order_data['billing']['last_name'];
        } else {
            $last_name = '';
        }

        $customer_name = $first_name."&nbsp;".$last_name;

        if(isset($order_data['billing']['email'])){
            $customer_email = $order_data['billing']['email'];
        } else {
            $customer_email = '';
        }
        

        $table_data .= "<tr class='".$order->get_status()."'>";
        $table_data .= "<td>" . $i . "</td>";
        $table_data .= "<td class='o_id'>" . $order_id . "</td>";
        $table_data .= "<td>" . $order->get_status() . "</td>";
        $table_data .= "<td>" .  $order_date_created . "</td>";
        $table_data .= "<td>".$customer_name."<br>".$customer_email."</td>";
        $table_data .= "</tr>";
     }
            
    return $table_data;
}


function bouw_update_order_status() {

    if(isset($_POST['orderArray']) && isset($_POST['status_to'])) {
        $orderArray = array_map('sanitize_key', json_decode(stripslashes($_POST['orderArray'])));
        $status_to = sanitize_text_field($_POST['status_to']);
        

        foreach($orderArray as $order_id) {
            $order = wc_get_order( $order_id );
            if(!empty($order)) {
               $order->update_status( $status_to,'Order Updated by bouw', true );
            }
        }
        
        $status = 200;
        $msg = 'Updating Order status to '.$status_to;
    } else {
        $status = 404;
        $msg = 'All Orders have been Updated';
    }
    echo json_encode(array('status'=>$status,'msg'=> $msg,'target_status'=>$status_to,'processed'=>count($orderArray),'order_array'=>$orderArray));
    exit();
}
// creating Ajax call for WordPress
add_action('wp_ajax_nopriv_bouw_update_order_status', 'bouw_update_order_status');
add_action('wp_ajax_bouw_update_order_status', 'bouw_update_order_status');
