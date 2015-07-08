<?php

/*
To do:
 - Paging
 - Caching (follow ttl?)

*/

class TWiTTV_API {

	const VERSION = 0.2;

	public $service_url = 'https://twit.tv/api/v1.0/';

	private $app_key;
	private $app_id;
	private $user_agent;

	public $request;
	public $response;

	private $class = array(
		'cache' => 'TWiTTV_Cache',
		'element' => 'TWiTTV_Element'
	);

	function __construct( $app_id, $app_key ) {
		$this->app_id     = $app_id;
		$this->app_key    = $app_key;
		$this->user_agent = $this->user_agent();
	}

	function request_endpoint( $endpoint, $param = array() ) {
		$url = $this->build_request_url( $endpoint, $param );
		return $this->request( $url );
	}

	function request( $url ) {
		$headers    = $this->request_headers();
		$cache      = $this->get_cache();
		$cache_key  = $this->cache_key( $url );
		$from_cache = false;
		$info       = array();
		$status     = '';

		// Attempt to retrieve from cache
		if ( $this->is_cache_enabled() && $response = $cache->get_item( $cache_key ) ) {
			$from_cache = true;
		} else {
			list( $response, $info ) = $this->http_request( $url, $headers );
			$status = $info['http_code'];
		}

		// Store in cache
		if ( ! $from_cache && $this->is_cache_enabled() && $response ) {
			$cache->set_item( $cache_key, $response );
		}

		$this->request = array(
			'url'        => $url,
			'headers'    => $headers,
			'cache'      => $this->is_cache_enabled()
		);

		$this->response = array(
			'body'       => $response,
			'from_cache' => $from_cache,
			'status'     => $status,
			'info'       => $info
		);

		return $this->response_object( $response );
	}

	function response_object( $data ) {
		return new $this->class['element']( $data );
	}

	private function build_request_url( $endpoint, $param ) {
		return $this->service_url . $endpoint . $this->build_query( $param );
	}

	private function build_query( $param ) {
		if ( empty( $param ) ) {
			return '';
		}

		return '?' . ( is_array( $param ) ? http_build_query( $param ) : $param );
	}

	private function request_headers() {
		$headers = array(
			'app-id: ' . $this->app_id,
			'app-key: ' . $this->app_key
		);

		return $headers;
	}

	private function user_agent() {
		return 'DeltaFactory_TWiT.TV_API/' . self::VERSION;
	}

	/**
	* Basic wrapper for easy curl HTTP requests.
	*/
	private function http_request( $url, $headers ) {
		$ch = curl_init( $url );
		curl_setopt_array( $ch, array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => true,
			CURLOPT_USERAGENT      => $this->user_agent,
			CURLOPT_HTTPHEADER     => $headers
		) );

		$body = curl_exec( $ch );
		$info = curl_getinfo( $ch );
		curl_close( $ch );

		return array( $body, $info );
	}

	// Caching
	function get_cache() {
		static $cache;

		if ( ! isset( $cache ) ) {
			$cache = new $this->class['cache']();
		}

		return $cache;
	}

	function is_cache_enabled() {
		$cache = $this->get_cache();
		return $cache && $cache->is_enabled();
	}

	function set_cache_path( $path, $create_if_missing = false ) {
		return $this->get_cache()->set_path( $path, $create_if_missing );
	}

	private function cache_key( $request_url ) {
		return 'twittv_cache_' . md5( $request_url ) . '.tmp';
	}

	// Traversing responses
	public function get_next( $obj ) {
		if ( $url = $obj->get_next_url() ) {
			$next = $this->request( $url );
		} else {
			$next = false;
		}

		return $next;
	}
}
