<?php

class GAS_Registry extends GAS {
	
	protected static $_instance;
	protected static $_data = array();
	
	public function __construct() {
		/**
		 *
		 */
	}
	
	public static function getInstance() {
	
		if( ( self::$_instance instanceof self ) !== true ) {
		
			self::$_instance = new self();
		
		}
		
		return self::$_instance;
			
	}
	
	public function toArray() {
	
		return self::$_data;
	
	}

	public function __get( $key ) {
	
		return $this->_get( $key );
	
	}
	
	public function __set( $key, $value ) {
	
		return $this->_set( $key, $value );
	
	}
	
	public function __isset( $key ) {
	
		return isset( self::$_data[ $key ] );
	
	}
	
	protected function _get( $key ) {
	
		if( array_key_exists( $key, self::$_data ) !== false ) {
		
			return self::$_data[ $key ];
		
		} else {
		
			return false;
		
		}
	
	}
	
	protected function _set( $key, $value ) {
	
		self::$_data[ $key ] = $value;
		
		return true;
	
	}

}