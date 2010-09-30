<?php

class GAS_Layout extends GAS {
	
	public $layoutData			= null;
	public $view				= null;
	
	protected $_layoutFile		= null;
	protected $_layoutPaths		= array();
	protected $_layoutSuffixes	= array( 'php', 'html', 'xhtml', 'phtml' );
	protected $_layout			= null;
	
	private $_layoutData		= array();
	private $_enabled			= true;
	private $_format			= 'http';
	
	public function __construct() {}
	
	public function setLayoutPath( $path ) {
		
		$this->_layoutPaths[] = $path;

		return $this;
	
	}
	
	public function setLayoutSuffx( $suffix ) {
		
		$this->_layoutPaths[] = $suffix;
		
		return $this;
	
	}
	
	public function getLayout( $layout ) {
	
		foreach( $this->_layoutPaths as $path ) {
			
			foreach( $this->_layoutSuffixes as $suffix ) {
							
				if( is_readable( APP_PATH . $path . $layout . '.' . $suffix ) !== false ) {
				
					return APP_PATH . $path . $layout . '.' . $suffix;
				
				}
			
			}
		
		}
		
		throw new GAS_Layout_Exception( 'Invalid layout given.' );
	
	}
	
	public function setLayout( $layout ) {
	
		$this->_layout	= $layout;
		
		return $this;
	
	}
	
	public function render( $layout = null ) {
		
		if( $layout === null ) {
			
			$layout	= $this->_layout;
		
		}
		
		$this->_layout	= $this->getLayout( $layout );
		
		$this->_parseView();
		
		ob_start();

		include $this->_layout;

        return $this->_setLayout( ob_get_clean() ); // filter output

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
	
	public function helper( $helper ) {
	
		$className = 'View_Helpers_' . ucwords( strtolower( $helper ) );

		if( class_exists( $className ) !== false ) {
		
			return new $className();
		
		} else {
		
			throw new GAS_Layout_Exception( 'Invalid helper requested (' . $helper . ')' );
		
		}
	
	}
	
	public function emptyVars() {
		
		$this->_layoutData	= array();
	
	}
	
	public function disableLayout() {
	
		$this->_enabled = false;
	
		return $this;
		
	}
	
	public function enableLayout() {
		
		$this->_enabled = true;
		
		return $this;
	
	}
	
	public function setFormat( $format ) {
	
		$this->_format = $format;
		
		return $this;
	
	}
	
	public function enabled() {
		
		return $this->_enabled;
	
	}
	
	public function format() {
	
		return $this->_format;
	
	}
	
	public function setView( GAS_View &$view ) {
		
		$this->view	= $view;
	
	}
	
	public function getView() {
		
		return $this->view;
	
	}
	
	public function __set( $key, $value ) {
	
		$this->_layoutData[ $key ] = $value;
	
	}
	
	public function __get( $key ) {

		if( array_key_exists( $key, $this->_layoutData ) !== false ) {
		
			return $this->_layoutData[ $key ];
		
		} else {
			
			return false;
		
		}
		
	}
	
	public function __call( $method, $arguments ) {
		
		if( $instance = $this->helper( $method ) ) {
		
			if( method_exists( $instance, 'init' ) !== false ) {
				
				call_user_func_array( array( $instance, 'init' ), $arguments );
			
			}
			
			return $instance;
		
		}
	
	}

    public function __isset( $key ) {
    	
    	return isset( $this->_layoutData[ $key ] );
    
    }

	protected function _setLayout( $data ) {
		
		$this->layoutData = $data;
		
		return $data;
	
	}
	
	protected function _parseView() {
	
		$this->content	= $this->view->viewData;
	
	}
	
	public function getViewContent() {
	
		$this->_parseView();
		
		return $this->content;
	
	}

}