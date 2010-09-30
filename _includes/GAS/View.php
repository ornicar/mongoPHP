<?php

class GAS_View extends ArrayObject {
	
	public $viewData				= null;
	protected $_viewScript			= null;
	protected $_viewScriptPaths		= array();
	protected $_viewScriptSuffixes	= array( 'php', 'html', 'xhtml', 'phtml' );
	protected $_script				= null;
	protected $_encoding			= 'UTF-8';
	private $_viewData				= array();
	
	public function __construct() {
		
		parent::__construct( array(), ArrayObject::ARRAY_AS_PROPS );
			
	}
	
	public function setViewScriptPath( $path ) {
		
		$this->_viewScriptPaths[] = $path;

		return $this;
	
	}
	
	public function setViewScriptSuffx( $suffix ) {
		
		$this->_viewScriptPaths[] = $suffix;
		
		return $this;
	
	}
	
	public function getView() {
	
		return $this->_viewScript;
	
	}
	
	public function getViewScript( $viewScript ) {
	
		foreach( $this->_viewScriptPaths as $path ) {
			
			foreach( $this->_viewScriptSuffixes as $suffix ) {
								
				if( is_readable( APP_PATH . $path . $viewScript . '.' . $suffix ) !== false ) {
				
					return APP_PATH . $path . $viewScript . '.' . $suffix;
				
				}
			
			}
		
		}

		throw new GAS_View_Exception( 'Invalid view script given.', 1 );
	
	}
	
	public function helper( $helper ) {
		
		$className = 'View_Helpers_' . ucwords( $helper );

		if( class_exists( $className ) !== false ) {
		
			return new $className();
		
		} else {
		
			throw new GAS_Layout_Exception( 'Invalid helper requested (' . $helper . ')' );
		
		}
	
	}

	public function setView( $viewScript ) {
		
		$this->_viewScript = $viewScript;
		
		return $this;
	
	}
	
	public function render( $viewScript = null ) {

		if( $viewScript === null ) {
			
			$viewScript	= $this->_viewScript;
		
		}
		
		$this->_script	= $this->getViewScript( $viewScript );
		
		if( function_exists( 'mb_internal_encoding' ) !== false ) {
			
			mb_internal_encoding( $this->_encoding );
			mb_http_output( $this->_encoding );
			mb_http_input( $this->_encoding );
			mb_language( 'uni' );
			mb_regex_encoding( $this->_encoding );
	
			ob_start( 'mb_output_handler' );
		
		} else {
		
			ob_start();
		
		}
		
		include $this->_script;

        return $this->_setView( ob_get_clean() ); // filter output

	}
	
	public function assign( $key, $value = null ) {
		
		if( is_array( $key ) !== false ) {
		
			foreach( $key as $index => $value ) {
				
				$this->__set( $index, $value );
			
			}
		
		} else {
			
			if( $value !== null ) {
				
				$this->__set( $key, $value );
			
			}
		
		}
	
	}
	
	public function emptyVars() {
		
		$this->_viewData	= array();
	
	}
	
	public function setEncoding( $encoding = 'UTF-8' ) {
	
		$this->_encoding	= $encoding;
	
	}
	
	public function __call( $method, $arguments ) {
	
		if( $instance = $this->helper( $method ) ) {
		
			if( method_exists( $instance, 'init' ) !== false ) {
				
				call_user_func_array( array( $instance, 'init' ), $arguments );
			
			}
			
			return $instance;
		
		}
	
	}

	protected function _setView( $data ) {
		
		$this->viewData = $data;
		
		return $data;
	
	}
	
}