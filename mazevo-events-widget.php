<?php

/*
Plugin Name: Mazevo Events Widget
Plugin URI: christinareeser.com
Description: Widget to display Mazevo Events using [mazevo-events-widget] shortcode. Optional parameter to set the number to display.
Version: 1.0
Author: Christina Reeser
Author URI: https://christinareeser.com/
License: GPLv2 or later
Text Domain: mazevo-events-widget
*/

function mazevo_events_widget_plugin( $atts, $content = null ) {
    extract(shortcode_atts(array(
        'limit'      => '5' //default events to show
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
        "x-api-key: YOUR API KEY HERE",
		'httpversion' => '1.1',
		'redirection' => 5, 
		'timeout' => 60,
        'sslverify' => false,
		'blocking' => true,
		'body' => json_encode($postData)
    );
    

	$response = wp_remote_post( 'YOUR REMOTE URL HERE', $context);


	if ( is_wp_error( $response ) ) {
	   $error_message = $response->get_error_message();
	   echo "Something went wrong: $error_message";
	} else {


	// Get all the events for the specified period
	$allData = json_decode(wp_remote_retrieve_body($response), true);
		
	// Sort them in the order of the start time field.
	usort($allData, function ($item1, $item2) {
    	return $item1['dateTimeStart'] <=> $item2['dateTimeStart'];
	});

	// Trim to array to the amount of events set. 5 is the default
	$responceData =  array_slice($allData, 0, $limit);
	
	$output = '<h3 class="widget-title">Upcoming Events</h3>';
	$output .= '<ul class="mazevo-upcoming-events">';     	
	foreach($responceData as $item) {

		//grab the start and end times
  		$startdate = $item['dateTimeStart'];
  		$endtime = $item['dateTimeEnd'];
  		
  		//convert to DateTime to make them readable
		$date = new DateTime($startdate);
		$enddate = new DateTime($endtime);
  		
  		$output .= '<li><span style="font-size: smaller; opacity: .8;">';  	 
  		$output .= $date->format('M j').' @ ';
   		$output .=  $date->format('g:i a').'-'.$enddate->format('g:i a').' | '.$item['roomDescription'].' | '.$item['buildingDescription'].'</span><br />';
		$output .= '<b>'.$item['eventName'].'</b>';
	
	}
	$output .= '</ul>';
	$output .= '<a href="https://east.mymazevo.com/calendar?code=aDJKNnNlbjFWK1kxVUZXZGlYdDU2c3JUbmdYR1h1T3g2NldVQlFuQnpMNEhBUEo4SisrbkRUNFRpb0xYcEExNzZyV3h2YVF1Rmd6bmVWd0xDamNlR3lWcmlrWlJCWkdXaW80U09PbE8zY0k9" class="button btn" target="_blank">View All Events</a>';
	 }
	return $output;  
}   

add_shortcode('mazevo-events-widget', 'mazevo_events_widget_plugin');
?>
