/*
* Onclick event for upload file button
* Once the CSV is selected it will trigger the next dropdown for selecting order ID
* Only CSV type file will show on media library
*    
*/
jQuery(document).ready(function($){
    jQuery('#upload-btn').click(function(e) {
        e.preventDefault();
        var csv = wp.media({ 
            title: 'Upload Your CSV File',
            button: {
                text: 'Select CSV'
            },
            // mutiple: true if you want to upload multiple files at once
            multiple: false,
            library: {
            	type: [ 'text/csv' ]
    		}
        }).open()
        .on('select', function(e){
            // This will return the selected image from the Media Uploader, the result is an object
            var uploaded_csv = csv.state().get('selection').first();
            // We convert uploaded_image to a JSON object to make accessing it easier
            // Output to the console uploaded_image
            var fileName = uploaded_csv['attributes']['filename'];
            var fileExtension = fileName.substr((fileName.lastIndexOf('.') + 1));
            //console.log (fileExtension)
            //console.log(uploaded_csv);
            if(fileExtension=='csv') {
                var csv_url = uploaded_csv.toJSON().url;
                // Let's assign the url value to the input field
                jQuery('#csv_url').val(csv_url);
                jQuery('.bouw-table-preview table').html('');
                bouw_fetch_csv_data(csv_url);
            } else {
                alert('Please Upload CSV filetypes only');
            }
        });
    });
});


/*
* After CSV Upload Dropdown will show up
* User needs to select the order ID column from the Dropdown
* Usually the first row cell value will append inside the Dropdown
*    
*/
function bouw_fetch_csv_data(csv_url) {
    jQuery('.bouw-order-config').html('');
    var form_data = new FormData();
    form_data.append('action', 'bouw_fetch_csv_data');
    form_data.append('csv_url', csv_url);
    create_pre_loader('Reading Rows From CSV');
    jQuery.ajax({
        url: wpAjax.ajaxurl,
        type: 'POST',
        dataType: 'JSON',
        contentType: false,
        processData: false,
        data: form_data,
        success: function(data){
            console.log(data);
            jQuery('.bouw-order-config').append(data['html']);
            destroy_pre_loader();
        },
        error : function(XMLHttpRequest, textStatus, errorThrown) {
            console.log("Error: " + errorThrown);
            destroy_pre_loader();
        }
    });
}

//dropdown change event to fetch the data from CSV via selected order id column
jQuery(document).on('change', 'select.select_order_field', function(){
    var column_key = jQuery(this).val();
    var csv_url = jQuery('#csv_url').val();
    jQuery('.bouw-table-preview table').html('');
    //console.log(column_key);
    if (csv_url===''){
        alert('Please Select a CSV File');
        jQuery('.bouw-table-action').slideUp();
    } else if(column_key === '') {
        alert('Please Select a Column');     
        jQuery('.bouw-table-action').slideUp();
    } else {
        jQuery('.bouw-table-preview table').append('<tr><th>Sl No</th><th>Order ID</th><th>Status</th><th>Placed on</th><th>Customer Information</th></tr>');
        create_pre_loader('Loading CSV Data');
        start_csv_to_html_init(csv_url, column_key);
    }
});

//starting interval to reduce the load time of sever and spliting the ajax response
function start_csv_to_html_init(csv_url, column_key){
    var i = 0;
    var bouw_interval = jQuery('.bouw_interval').val();
    csvLoad = setInterval(function(){ 
        i++;
        //console.log(i);
        bouw_generate_csv_table(csv_url, column_key,i);
    }, bouw_interval);
}


function bouw_generate_csv_table(csv_url, column_key, i) {
    var $peritem = jQuery('.bouw_interval_per_item').val();
    var form_data = new FormData();
    form_data.append('action', 'bouw_generate_csv_table');
    form_data.append('csv_url', csv_url);
    form_data.append('column_key', column_key);
    form_data.append('interval', i);
    form_data.append('peritem', $peritem);
    jQuery.ajax({
        url: wpAjax.ajaxurl,
        type: 'POST',
        dataType: 'JSON',
        contentType: false,
        processData: false,
        data: form_data,
        success: function(data){
            console.log(data);
            if(jQuery('.bouw-table-preview').find('tr#error_column').length==0 && jQuery('.bouw-table-preview').find('tr#loaded_column').length==0){
                jQuery('.bouw-table-preview').find('table').append(data['html']);
                jQuery('.bouw_pg_total').text(data['total']);
                jQuery('.bouw_pg_current').text(data['current']);
                //jQuery(".bouw-table-preview table").animate({ scrollTop: jQuery('.bouw-table-preview table').prop("scrollHeight")}, 1000);
            }
            if(data['status']==404) {
                console.log('Stopping Databinding Process');
                clearTimeout(csvLoad);
                destroy_pre_loader();
                jQuery('.bouw-table-action').slideDown();
            }
        },
        error : function(XMLHttpRequest, textStatus, errorThrown) {
            console.log("Error: " + errorThrown);
            destroy_pre_loader();
        }
    });
}

//onclick start updating the order status based on the status dropdown selection
jQuery(document).on('click', 'button.bouw-change-status', function() {
    var status_from = jQuery('select.status_from').val();
    var status_to = jQuery('select.status_to').val();
    if(status_from==status_to){
        alert('Order Status Cant be same as Current Status');
        
    } else if(jQuery('.bouw-table-preview').find('tr#error_column').length!=0) {
        alert('No Order found in the CSV');
    } else if(jQuery('.bouw-table-preview').find('tr.'+status_from).length==0) {
        alert('No Order with matching Status');
    } else {
        create_pre_loader('Updating Order Status');
        var total_Selected_order = jQuery('.bouw-table-preview').find('tr.'+status_from).length;
        jQuery('.bouw_pg_total').text(total_Selected_order);
        
        start_order_update_init(status_from,status_to);
    }
});

//starting interval to reduce the load time of sever and spliting the ajax response
function start_order_update_init(status_from,status_to) {
    var bouw_interval = jQuery('.bouw_interval').val();
    var i = 0;
    odrUpdate = setInterval(function(){
        i++;
        bouw_select_order_ids(status_from,status_to,i);
    }, bouw_interval);
}

function bouw_select_order_ids(status_from,status_to,i) {
    var orderArray = [];
    var $peritem = jQuery('.bouw_interval_per_item').val();
    if(i===1) {
       var $start_node = 0;
       var $end_node = $peritem;
    } else {
        var $end_node = $peritem*i;
        var $start_node = $end_node-$peritem+1;
    }

    for(var j=$start_node;j<=$end_node;j++) {
        if(jQuery('.bouw-table-preview table tr.'+status_from).eq(j).length!=0){
            var order_id = jQuery('.bouw-table-preview table tr.'+status_from).eq(j);
            orderArray.push(order_id.find('td.o_id').text());
        } else {
            break;
        }

    }
   
    if(orderArray.length!==0) {
        console.log(orderArray);
        bouw_update_order(orderArray,status_to);
    } else {
        var column_key = jQuery('select.select_order_field').val();
        var csv_url = jQuery('#csv_url').val();
        clearTimeout(odrUpdate);
        destroy_pre_loader();
        alert('All Orders with Selected Status have been updated');
        jQuery('.bouw-table-preview table').html('');
        jQuery('.bouw-table-preview table').append('<tr><th>Sl No</th><th>Order ID</th><th>Status</th><th>Placed on</th><th>Customer Information</th></tr>'); 
        create_pre_loader('Reloading Updated CSV Data');
        start_csv_to_html_init(csv_url, column_key);
    }
}

function bouw_update_order(orderArray,status_to) {

    var orderArray = JSON.stringify(orderArray);
    var form_data = new FormData();
    form_data.append('action', 'bouw_update_order_status');
    form_data.append('orderArray', orderArray);
    form_data.append('status_to', status_to);
    jQuery.ajax({
        url: wpAjax.ajaxurl,
        type: 'POST',
        dataType: 'JSON',
        contentType: false,
        processData: false,
        data: form_data,
        success: function(data){
            console.log(data);
            var current = jQuery('.bouw_pg_current').text();
            current = parseInt(current) + data['processed']
            jQuery('.bouw_pg_current').text(current);
        },
        error : function(XMLHttpRequest, textStatus, errorThrown) {
            console.log("Error: " + errorThrown);
            destroy_pre_loader();
        }
    });
}

function create_pre_loader(data_message){
    jQuery('div.bouw_preload span.bouw_loader_message').text(data_message);
    jQuery('div.bouw_preload').show();
}

function destroy_pre_loader(){
    //clearTimeout(preLoad);
    jQuery('div.bouw_preload').hide();
    jQuery('div.bouw_preload span.bouw_loader_message').text('');
    jQuery('.bouw_pg_current').text('0');
    jQuery('.bouw_pg_total').text('0');
}

jQuery(document).on('click','.bouw_settings',function() {
    jQuery('.bouw-config-panel').slideToggle();
});


