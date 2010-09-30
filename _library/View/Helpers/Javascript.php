<?php

class View_Helpers_Javascript extends GAS_View_Helper_Abstract {

	protected static	$_registry;
	protected			$_controller;
	protected			$_action;
	protected			$_mainPath;
	protected			$_javascriptPath;
	protected			$_includeInternal	= true;
	protected			$_paths				= array();
	private				$_javascript		= array();
	private				$_config			= array();
	
	public function __construct() {
	
		if( self::$_registry === null ) {
			
			self::$_registry = GAS_Registry::getInstance();
		
		}

		$this->_config			 = static::$_registry->config;
		$this->_controller		 = static::$_registry->controller;
		$this->_action			 = static::$_registry->action;
		$this->_javascriptPath	 = static::$_registry->config->application->path;
		$this->_mainPath		 = $this->_javascriptPath;
		$this->_javascriptPath	.= $this->_config->site->javascript->path;
		$this->_getRequired();
		$this->_buildLinks();
	
	}
	
	protected function _getRequired() {
	
		$required		 = $this->_config->site->javascript->required->toArray();
		$required		 = (array)$required[ 'file' ];
				
		foreach( $required as $index => $value ) {
			
			$include = true;
			
			if( preg_match( '/http:\/\//is', $value ) === 0 ) {
			
				$path			= $this->_javascriptPath . $value . '.js';
				$this->_paths[]	= GAS_Bootstrap::$appPath . $this->_config->site->javascript->path . $value . '.js';						
				$include		= $this->_includeInternal;
			
			} else {
				
				$path	= $value;
				
			}
			
			if( $include === true ) {
			
				$this->_javascript[]	= '<script type="text/javascript" src="' . $path . '"></script>';
		
			}
			
		}
		
	}

	protected function _buildLinks() {
				
		$files		= array();
		$jsBefore	= GAS_Controller_Front::getFront()->view->jsAdded[ 'before' ];
		$jsBefore	= array_unique( $jsBefore );

		foreach( $jsBefore as $index => $value ) {
			
			$include = true;
			
			if( preg_match( '/http:\/\//is', $value ) === 0 ) {
				
				$path			= $this->_javascriptPath . $value . '.js';
				$this->_paths[]	= GAS_Bootstrap::$appPath . $this->_config->site->javascript->path . $value . '.js';						
				$include		= $this->_includeInternal;
				
			} else {
				
				$path	= $value;
			
			}
			
			if( $include === true ) {
			
				$this->_javascript[] = '<script type="text/javascript" src="' . $path . '"></script>';
			
			}
			
		}

		$controllerJs	= GAS_Controller_Front::getFront()->view->js;
		$controllerJs	= array_unique( $controllerJs );

		foreach( $controllerJs as $index => $value ) {
			
			$include = true;
			
			if( preg_match( '/http:\/\//is', $value ) === 0 ) {
			
				$path			= $this->_javascriptPath . $value . '.js';
				$this->_paths[]	= GAS_Bootstrap::$appPath . $this->_config->site->javascript->path . $value . '.js';						
				$include		= $this->_includeInternal;

			} else {
				
				$path	= $value;
			
			}
			
			if( $include === true ) {
			
				$this->_javascript[] = '<script type="text/javascript" src="' . $path . '"></script>';
			
			}
			
		}
		
		if( $this->_includeInternal === true ) {
	
			$files[]	= $this->_controller . '.js';
			$files[]	= $this->_controller . '/' . $this->_action . '.js';
			$files[]	= $this->_controller . '/' . $this->_controller . '.js';
			$files		= array_unique( $files );
			$size		= count( $files );

			for( $i = 0; $i < $size; $i++ ) {
				
				$file = GAS_Bootstrap::$appPath . $this->_config->site->javascript->path . $files[ $i ];

				if( file_exists( $file ) !== false ) {
				
					$this->_paths[]			= $file;						
					$this->_javascript[]	= '<script type="text/javascript" src="' . $this->_javascriptPath . $files[ $i ] . '?' . filemtime( $file ) . '"></script>';
				
				}
			
			}
	
		}
		
		$jsAfter	= GAS_Controller_Front::getFront()->view->jsAdded[ 'after' ];
		$jsAfter	= array_unique( $jsAfter );
		
		foreach( $jsAfter as $index => $value ) {
		
			$include = true;
			
			if( preg_match( '/http:\/\//is', $value ) === 0 ) {
			
				$path			= $this->_javascriptPath . $value . '.js';
				$this->_paths[]	= GAS_Bootstrap::$appPath . $this->_config->site->javascript->path . $value . '.js';
				$include		= $this->_includeInternal;

			} else {
				
				$path	= $value;
			
			}
		
			if( $include === true ) {
			
				$this->_javascript[] = '<script type="text/javascript" src="' . $path . '"></script>';
			
			}
			
		}
		
	}

	public function __toString() {

		$controller 	= static::$_registry->controller;
		$action			= static::$_registry->action;
	
		return implode( "\n		", $this->_javascript ) . "\n		";

	}

}