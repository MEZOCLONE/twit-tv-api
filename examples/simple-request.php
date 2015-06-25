<?php
/*
	TWiT.tv API Example: A Simple Request

	This script demonstrates a basic request to the API using PHP cURL. The URL provided
	retrieves the entry for the latest episode and uses the data to print a simple message.
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
$response = curl_exec( $ch );

//   Close cURL handle resource.
curl_close( $ch );


// Turn JSON response into a PHP object
$json = json_decode( $response );

// Grab the parts we need, do something useful with it!
$episode = $json->episodes[0];
$show = $episode->shows[0];

echo 'The most recent episode is ' htmlspecialchars( $show->label . ': ' . $episode->label );
