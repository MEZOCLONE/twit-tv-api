<?php

class TWiTTV_Element {

	private $_api;
	private $_data;

	function __construct( $source ) {
		$this->json = $source;
		$this->_data = json_decode( $source );
	}

	function __get( $key ) {
		return $this->_data->{$key};
	}

	function __isset( $key ) {
		return isset( $this->_data->{$key} );
	}

	private function _get_node( $node, $key = false ) {
		$n = isset( $this->{$node} ) ? $this->{$node} : array();

		if ( $key !== false && isset( $n->{$key} ) ) {
			$n = $n->{$key};
		}

		return $n;
	}

	function get_embedded( $key = false ) {
		return $this->_get_node( '_embedded', $key );
	}

	function get_links( $key = false ) {
		return $this->_get_node( '_links', $key );
	}

	function get_next_url() {
		$url = false;

		if ( $next = $this->get_links( 'next' ) ) {
			$url = $next->href;
		}

		return $url;
	}

}
