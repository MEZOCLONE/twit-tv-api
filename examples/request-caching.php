<?php
/*
	TWiT.tv API Example: A Simple Request with Caching
	by Jeff Brand

	This script demonstrates basic caching of API requests starting with the call demonstrated in simple-request.php.

	More Info:
	* API credentials: https://twit.tv/about/developer-program
	* API documentation: http://docs.twittv.apiary.io/

	This work is licensed under a Creative Commons Attribution-ShareAlike 4.0 International License.
*/

// Basics
$app_id = 'YOUR APP ID HERE';
$app_key = 'YOUR APP KEY HERE';
$url = 'https://twit.tv/api/v1.0/episodes?range=1';

// Storage of cache files. The specified directory must exist and be writeable by this script.
$cache_dir = __DIR__ . '/cache';

// Create cache directory if it doesn't exist yet.
if ( ! file_exists( $cache_dir ) ) {
	mkdir( $cache_dir );
}

// Create a unique filename based on the URL.
$cache_file = $cache_dir . '/twitapi_cache_' . md5( $url ) . '.tmp';

// How old, in seconds, can the cached file be before expiring?
$cache_timeout = 600;

// Check for a cache file that hasn't expired.
if ( file_exists( $cache_file ) && is_readable( $cache_file ) && ( time() - filemtime( $cache_file ) > $timeout ) ) {
	$json = file_get_contents( $cache_file );
} else {
	// Only make the HTTP request if we can't find a recent copy locally.
	// * This is essentially identical to the Simple Request example.

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

	// ** If we have a successful response, store the result in the cache for later.
	if ( $json ) {
		file_put_contents( $cache_file, $json );
	}
}

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
