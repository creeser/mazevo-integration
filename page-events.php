<?php
/**
 * Template Name: Mazevo Events 
 *
 * This template is the default page template. It is used to display content when someone is viewing a
 * singular view of a page ('page' post_type) unless another page template overrules this one.
 * @link http://codex.wordpress.org/Pages
 *
 * @package Hybrid
 * @subpackage Template
 */

get_header(); ?>

<style type="text/css">
.dt-nowrap {  white-space: nowrap;}
</style>

<div style="width: 90%; margin: 0 auto; overflow:scroll">

<?php
$start = date('c');
$end = date('c', strtotime("+1 months", strtotime("NOW"))); 
 
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

$response = wp_remote_post( 'YOUR URL HERE', $context);

if ( is_wp_error( $response ) ) {
  $error_message = $response->get_error_message();
  echo "Something went wrong: $error_message";
} else {
  $responseData = json_decode(wp_remote_retrieve_body($response), true);
}
?>

<script type="text/javascript">
var information = <?php echo json_encode($responseData) ?>;

$(document).ready(function () {
    $('#event-table').dataTable({
        data: information,
        columns: [
            { data: 'dateTimeStart', 
           		title: 'Date', 
           		"render": function (data) {
        			var date = new Date(data);
        			var month = date.getMonth() + 1;
        			return (month.toString().length > 1 ? month : "0" + month) + "/" + date.getDate() + "/" + date.getFullYear();
        		}
            },
            { data: 'dateTimeStart', 
           		title: 'Start', 
           		className: "dt-nowrap",
         	  "render": function (data) {
        			var date = new Date(data);
        			return (date.toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true }));
        		}
            },
           
            { data: 'dateTimeEnd', 
           		title: 'End', 
           		className: "dt-nowrap",
           		"render": function (data) {
        			var date = new Date(data);
        			return (date.toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true }));
        		}
            },
            { data: 'eventName', title: 'Event Name' },
            { data: 'buildingDescription', title: 'Building' },
            { data: 'roomDescription', title: 'Room' },
            { data: 'contactName', title: 'Contact' }
        ]
    });
});
</script>

	<table id="event-table" class="display">
        <thead>
            <tr>
              <th>Date</th> <th>Start</th>  <th>End</th> <th>Event Name</th> <th>Building</th> <th>Room</th> <th>Contact</th> 
            </tr>
        </thead>
        <tfoot> 
        <tr> 
        <th>Date</th> <th>Start</th>  <th>End</th> <th>Event Name</th> <th>Building</th> <th>Room</th> <th>Contact</th> 
        </tr> 
        </tfoot>
    </table>
    
</div>

<?php get_footer();
