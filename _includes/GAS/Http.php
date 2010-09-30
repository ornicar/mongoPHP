<?php

class GAS_Http extends GAS {

	public static function formatHeader( $header ) {
	
		if( strstr( '-', $header ) !== false ) {
			
			$header	= explode( '-', $header );
			$header	= array_walk( $header, function( &$value, $key ) {
			
				$value = ucwords( $value );
			
			});
			$header	= implode( '-', $header );
		
		}
				
		return $header;

	}
	
	public static function setHeader( $header, $value = null ) {
		
		$header	= static::formatHeader( $header );
				
		if( $value === null ) {
		
			return header( $header );

		} else {
		
			return header( $header . ': ' . $value );
		
		}
		
	}
	
	public static function isAjax() {
	
		return ( isset( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) && ( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] == 'XMLHttpRequest' ) );
	
	}

}