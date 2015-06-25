<?php

$app_id = 'YOUR APP ID HERE';
$app_key = 'YOUR APP KEY HERE';
$url = 'https://twit.tv/api/v1.0/episodes?range=1';

// Storage of cache files. The specified directory must exist and be writeable by this script.
$cache_path = __DIR__ . '/cache';

// Only make the HTTP request if we can't find a recent copy locally.
if ( ! $response = get_from_cache( $url ) ) {

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

	//   Make actual HTTPS request.
	$response = curl_exec( $ch );

	//   Clean up.
	curl_close( $ch );
}

// Turn JSON response into a PHP object
$json = json_decode( $response );

// Grab the parts we need, do something useful with it!
$episode = $json->episodes[0];
$show = $episode->shows[0];

echo 'The most recent episode is ' htmlspecialchars( $show->label . ': ' . $episode->label );

// Basic caching 
function get_from_cache( $path, $url ) {
	// How long should we cache a URL, in seconds?
	$timeout = 600;

	// Create a unique filename based on the URL.
	$cache_key = md5( $url );

	// Add some bits to help us remember that this is a temporary cache file.
	$cache_file = $path . '/twitapi_cache_' . $cache_key . '.tmp';

	// Initialize the return value to assume the worst.
	$result = false;

	// Can we find it?
	if ( file_exists( $cache_file ) && is_readable( $cache_file ) ) {

		if ( time() - filemtime( $cache_file ) > $timeout ) {
		// If it's too old based on file modification time, delete it.
			unlink( $cache_file );

		} else {
		// Otherwise, retrieve the contents
			$result = file_get_contents( $cache_file );

		}
	}

	return $result;
}
