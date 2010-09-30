<?php

class View_Helpers_UrlConstruct extends GAS_View_Helper_Abstract {

	protected $_parameters;
	
	public function init( $parameters ) {
	
		$this->_parameters = $parameters;
	
	}

	public function __toString() {
	
		$uri		= $_SERVER[ 'REQUEST_URI' ];
		$config		= GAS_Bootstrap::getConfig();
		$uri		= explode( $config->site->baseurl, $uri );
		
		unset( $uri[ 0 ] );
		
		$uri		= implode( '/', $uri );
		$explode	= explode( '/', $uri );
		$done		= array();
		
		foreach( $explode as $int => $part ) {
			
			if( array_key_exists( $part, $this->_parameters ) !== false ) {
				
				$explode[ ( $int + 1 ) ]	= $this->_parameters[ $part ];				
				$done[ $part ] 				= true;

			}
		
		}
		
		foreach( $this->_parameters as $part => $value ) {
		
			if( array_key_exists( $part, $done ) !== true ) {
			
				$explode[] = $part;
				$explode[] = $value;
			
			}
		
		}
		
		$url	 = SITE_ROOT;
		$url	.= str_replace( '//', '/', @implode( '/', $explode ) );
		
		return $url;
		
	}

}