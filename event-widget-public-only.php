/**
* Mazeva event widget
* Similar to the other examples but this widget 
* looks for private bookings and removes them 
* from the list
*/

<?php
		
	$today = strtotime('today 7am');
	$start =  date('Y-m-d\TH:i:s+00:00', $today);
	
	$tonight = strtotime('today 11:59pm');
	$end =  date('Y-m-d\TH:i:s+00:00', $tonight);
	
 
	$postData = array(
    'start' => $start,
    'end' => $end,
	);
	

	$context = array(
       'method' => 'POST',
        'headers' => "Authorization:\r\n".
        "Content-Type: application/json\r\n".
        "x-api-key: YOUR API KEY \r\n",
		'httpversion' => '1.1',
		'redirection' => 5, 
		'timeout' => 60,
        'sslverify' => false,
		'blocking' => true,
		'body' => json_encode($postData)
    );
    
   
	$response = wp_remote_post( 'https://api-east.mymazevo.com/api/PublicEvent/getevents', $context);
 	/*var_dump($response);*/

	if ( is_wp_error( $response ) ) {
	   $error_message = $response->get_error_message();
	   echo "Something went wrong: $error_message";
	} else {


	// Get all the events for the specified period
	$allData = json_decode(wp_remote_retrieve_body($response), true);
	
		
		
	// Sort them in the order of the start time field
	usort($allData, function ($item1, $item2) {
    return $item1['dateTimeStart'] <=> $item2['dateTimeStart'];
	});
	
	
	
//SEARCH values
function search($array, $key, $value) {
    $results = array();
      
    // if it is array
    if (is_array($array)) {
          
        // if array has required key and value
        // matched store result 
        if (isset($array[$key]) && $array[$key] == $value) {
            $results[] = $array;
        }
          
        // Iterate for each element in array
        foreach ($array as $subarray) {
              
            // recur through each element and append result 
            $results = array_merge($results, 
                    search($subarray, $key, $value));
        }
    }
  
    return $results;
}

	
	
	// use that function to make a new array with only public events
	$res = search($allData, 'bookingPrivate', false);

	// Trim to array to the amount of events set. 5 is the default
	$responceData =  array_slice($res, 0, '-1');
	
	
	$output = '<h3 class="widget-title">Upcoming Events</h3>';
	echo '<ul class="mazevo-upcoming-events">';     	
	foreach($responceData as $item) {

		//grab the start and end times
  		$startdate = $item['dateTimeStart'];
  		$endtime = $item['dateTimeEnd'];
  		
  		//convert to DateTime to make them readable
		$date = new DateTime($startdate);
		$enddate = new DateTime($endtime);
  		
  		echo '<li><span style="font-size: smaller; opacity: .8;">';  	 
  		echo $date->format('M j').' @ ';
   		echo  $date->format('g:i a').'-'.$enddate->format('g:i a').' | '.$item['roomDescription'].' | '.$item['buildingDescription'].'</span><br />';
		echo '<b>'.$item['eventName'].'</b>';
	
	}
	echo '</ul>';
	echo '<a href="https://east.mymazevo.com/calendar?code=aDJKNnNlbjFWK1kxVUZXZGlYdDU2c3JUbmdYR1h1T3g2NldVQlFuQnpMNEhBUEo4SisrbkRUNFRpb0xYcEExNzZyV3h2YVF1Rmd6bmVWd0xDamNlR3lWcmlrWlJCWkdXaW80U09PbE8zY0k9" class="button btn" target="_blank">View All Events</a>';
	 }
 
 
?>
