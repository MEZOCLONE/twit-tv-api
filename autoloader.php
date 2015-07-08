<?php

function twittv_api_autoloader( $class ) {
	$path = __DIR__ . '/src/';
	$prefix = 'twittv_';
	$class = strtolower( $class );

	if ( 0 !== strpos( $class, $prefix ) ) {
		return;
	}

	$file = str_replace( array( '_', '\\' ), '-', substr( $class, strlen( $prefix ) ) ) . '.php';

	if ( file_exists( $path . $file ) ) {
		include( $path . $file );
	}
}

spl_autoload_register( 'twittv_api_autoloader' );
