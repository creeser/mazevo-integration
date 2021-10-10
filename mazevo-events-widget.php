<?php

/*
Plugin Name: Mazevo Events Widget
Plugin URI: christinareeser.com
Description: Widget to display Mazevo Events using [mazevo-events-widget] shortcode. Optional parameter is available to set the number to display.
Version: 1.0
Author: Christina Reeser
Author URI: https://christinareeser.com/
License: GPLv2 or later
Text Domain: mazevo-events-widget
*/

function mazevo_events_widget_plugin( $atts, $content = null ) {
    extract(shortcode_atts(array(
        'limit'      => '5' 
    ), $atts));

 
	$start = date('c');
	$end = date('c', strtotime("+1 day", strtotime("NOW"))); 
 
	$postData = array(
    'start' => $start,
    'end' => $end,
	);
	$context = array(
        'method' => 'POST',
        'headers' => "Authorization:\r\n".
        "Content-Type: application/json\r\n".
        "x-api-key: YOUR API KEY HERE\r\n",
		'httpversion' => '1.1',
		'redirection' => 5, 
		'timeout' => 60,
        'sslverify' => false,
		'blocking' => true,
		'body' => json_encode($postData)
    );
    

	$response = wp_remote_post( 'YOUR MAZEVO URL HERE', $context);

	if ( is_wp_error( $response ) ) {
	   $error_message = $response->get_error_message();
	   echo "Something went wrong: $error_message";
	} else {

	$allData = json_decode(wp_remote_retrieve_body($response), true);
	$responceData =  array_slice($allData, 0, $limit);

	
	$output = '<h3 class="widget-title">Upcoming Events</h3>';
	$output .= '<ul style="line-height: 1; margin-bottom: 30px;">';     	
	foreach($responceData as $item) {

  	$startdate = $item['dateTimeStart'];
  	$endtime = $item['dateTimeEnd'];
  	
  	$output .= '<li style="margin-bottom: 15px; line-height: 1.2"><span style="font-size: smaller; color: #666;">'.date("M j", strtotime($startdate)).' @ ';
   	$output .=  date("g:i A", strtotime($startdate)).'</span><br />';
	$output .= '<b>'.$item['eventName'].'</b><br />';
	$output .= $item['roomDescription'];
	}
	$output .= '</ul>';
    }
	return $output;  
}   

add_shortcode('mazevo-events-widget', 'mazevo_events_widget_plugin');
?>
