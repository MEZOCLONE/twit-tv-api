<?php

class TWiTTV_Cache {

	private $path;

	private $timeout = 600;

	function __construct( $cache_path = false ) {
		$this->set_path( $cache_path );
	}

	function is_enabled() {
		return $this->path !== false;
	}

	function set_path( $path, $create_if_missing = false ) {

		// Disable cache.
		if ( $path === false ) {
			$this->path = false;
		} else {
			if ( ! file_exists( $path ) && $create_if_missing ) {
				mkdir( $path );
			}

			if ( is_dir( $path ) && is_writeable( $path ) ) {
				$this->path = $path;
			}
		}

		return $this->path;
	}

	function get_path() {
		return $this->path;
	}

	function get_default_timeout() {
		return $this->timeout;
	}

	function set_default_timeout( $val ) {
		$this->timeout = (int) $val;
	}

	function get_timeout( $key ) {
		// Per-item timeouts

		return $this->get_default_timeout();
	}

	private function serialize_callback() {
		return 'serialize';
	}

	private function unserialize_callback() {
		return 'unserialize';
	}

	function get_item( $key ) {

		if ( ! $this->is_enabled() || empty( $key ) ) {
			return false;
		}

		$cache_file = $this->get_path() . '/' . $key;
		$response = false;

		if ( file_exists( $cache_file ) ) {
			if ( ( time() - filemtime( $cache_file ) ) < $this->get_timeout( $key ) ) {
				$response = call_user_func( $this->unserialize_callback(), file_get_contents( $cache_file ) );
			} else {
				//Cleanup expired cache items.
				$this->delete_item( $key );
			}
		}

		return $response;
	}

	function set_item( $key, $data ) {
		$can_cache = $this->is_enabled();
		$cache_file = $this->get_path() . '/' . $key;

		if ( $can_cache && !file_exists( $cache_file ) || is_writable( $cache_file ) ) {
			file_put_contents( $cache_file, call_user_func( $this->serialize_callback(), $data ) );
		}

		return $can_cache;
	}

	function delete_item( $key ) {
		if ( empty( $this->path ) || empty( $key ) ) {
			return false;
		}

		$file = $this->path . '/' . $key;
		if ( is_file( $file ) ) {
		//	return unlink( $this->path . '/' . $key );
			echo 'UNLINK: ' . $this->path . '/' . $key;
		}

		return false;
	}

	function clear_cache() {
		if ( $this->is_enabled() ) {
			unlink( $this->path . '/twittv_cache_*.tmp' );
		}
	}
}
