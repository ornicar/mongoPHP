<?php

class GAS_Session_Namespace extends GAS_Session {

	protected static $_instances = array();

	public final function __construct( $namespace ) {
		
		$this->_prefix = $namespace;
		
		parent::__construct();
	
	}
	
	public static function factory( $namespace ) {
	
		if( isset( static::$_instances[ $namespace ] ) !== true ) {
		
			static::$_instances[ $namespace ] = new self( $namespace );
		
		}
		
		return static::$_instances[ $namespace ];
	
	}

}