<?php

class GAS_Loader extends GAS {

	public $paths = array(
		
		'includes'		=> '_includes/',
		'libraries'		=> '_library/',
		'application'	=> '_application/',
		'controllers'	=> '_application/controllers/',
		'models'		=> '_application/models/',
		'views'			=> '_application/views/'
	
	);

	protected $_namespaces	= array(
	
		'GAS_'
	
	);
	
	protected static $_instance;
	
	public function __construct() {

		spl_autoload_register( array( $this, 'autoload' ) );

    }

	public static function getInstance() {
	
		if( ( self::$_instance instanceof self ) !== true ) {
		
			self::$_instance = new self();
		
		}
		
		return self::$_instance;
	
	}
	
	public static function registerNamespace( $namespace ) {
	
		self::getInstance()->_namespaces[ $namespace ] = true;	
	
	}
	
	public function autoload( $className ) {
		
		$parts	= explode( '_', $className );
		$path	= str_replace( '_', '/', $className );

		foreach( $this->paths as $_type => $_path ) {
			
			$fullPath = GAS_Bootstrap::$appPath . $_path . $path . '.php';

			if( file_exists( $fullPath ) !== false ) {
				
				include_once $fullPath;
				
				break;
				
			}
			
		}

	}

}