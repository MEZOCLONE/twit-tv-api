<?php
/*
	TWiT.tv API Example: A Simple Request
	by Jeff Brand

	This script demonstrates a basic request to the API using PHP cURL. The URL provided
	retrieves the entry for the latest episode and uses the data to print a simple message.

	More Info:
	* API credentials: https://twit.tv/about/developer-program
	* API documentation: http://docs.twittv.apiary.io/

	This work is licensed under a Creative Commons Attribution-ShareAlike 4.0 International License.
*/

// Basics
$app_id = 'YOUR APP ID HERE';
$app_key = 'YOUR APP KEY HERE';
$url = 'https://twit.tv/api/v1.0/episodes?range=1';

// Build HTTP headers for curl with API credentials
$headers = array(
	'app-id: ' . $app_id,
	'app-key: ' . $app_key
);

// Use cURL to make HTTP request:

//   Setup cURL handler with URL to be called.
$ch = curl_init( $url );

//   Set headers and make sure we get response sent back during curl_exec()
curl_setopt_array( $ch, array(
	CURLOPT_HTTPHEADER     => $headers,
	CURLOPT_RETURNTRANSFER => true
) );

//   Make the request and store the JSON response.
$json = curl_exec( $ch );

//   Close cURL handle resource.
curl_close( $ch );

// Turn JSON response into a PHP object
$response_obj = json_decode( $json );

// Check that the response was JSON and was processed correctly.
if ( $response_obj !== null ) {

	// Grab the parts we need, do something useful with it!
	$episode = $response_obj->episodes[0];
	$show = $episode->_embedded->shows[0];

	echo 'The most recent episode is ' . htmlspecialchars( $show->label . ': ' . $episode->label ) . "\n";
} else {

	// Something went wrong. Display an error message.
	echo "No response. Check your App ID, Key, and request URL for errors.\n";
}