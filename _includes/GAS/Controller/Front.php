<?php

class GAS_Controller_Front extends GAS_Controller {
	
	protected $_router;
	protected static $_instance;
	protected static $_definitions;
	protected static $_layout;
	protected static $_registry;
	protected static $_controller;
	
	public function __construct() {
	
		static::_prepare();
		static::$_registry = GAS_Registry::getInstance();
	
	}
	
	public static function getInstance() {
	
		if( ( static::$_instance instanceof static ) !== true ) {
		
			static::$_instance = new static();
		
		}
		
		return static::$_instance;
	
	}
	
	public static function getFront() {
	
		return self::$_controller;
	
	}
	
	public function setControllerDirectory( $controllerDirectory ) {
		
		static::$_definitions->controllerDirectory = $controllerDirectory;
		
		return $this;

	}

	public function setViewDirectory( $viewDirectory ) {
		
		static::$_definitions->viewDirectory = $viewDirectory;

		return $this;
		
	}

	public function setViewScriptsDirectory( $viewDirectory ) {
		
		static::$_definitions->viewScriptsDirectory = $viewDirectory;
		
		return $this;
	
	}

	public function setLayoutDirectory( $layoutDirectory ) {
		
		static::$_definitions->layoutDirectory = $layoutDirectory;
		
		return $this;
	
	}
	
	public function setBaseUrl( $baseUrl ) {

		static::$_definitions->baseUrl = $baseUrl;
	
		return $this;
	
	}
	
	public function setDefaultControllerName( $defaultController ) {
	
		static::$_definitions->defaultController = $defaultController;
	
		return $this;
	
	}
	
	public function setRouter( GAS_Controller_Router $router ) {
		
		$this->_router	= $router;

		return $this;

	}
	
	public function getDefinitions() {
		
		return static::$_definitions;
	
	}
	
	public function route() {

		if( $this->_router === null ) {
		
			$this->setRouter( new GAS_Controller_Router( static::$_definitions ) );
		
		}
		
		return $this->_router;
		
	}
	
	public function attachLayout( GAS_Layout &$layout ) {
	
		static::$_layout = $layout;
	
	}
	
	public static function getLayout() {
	
		return static::$_layout;
	
	}
	
	public function dispatch( $controllerName = null, $actionName = null ) {
	
		$route = $this->route()->route()->returnRoute();

		if( $controllerName !== null ) {
		
			$route->controller = strtolower( $controllerName );
		
		}
		
		if( $actionName !== null ) {
		
			$route->action = strtolower( $actionName );

		}

		$controllerName	= ucwords( strtolower( $route->controller ) ) . 'Controller';
		$actionName		= ucwords( $route->action ) . 'Action';

		if( file_exists( APP_PATH . static::$_definitions->controllerDirectory . $controllerName . '.php' ) !== true ) {
		
			/**
			 * Controller exists
			 */
			$controllerName	= 'ErrorController';
		
		}
		
		static::$_registry->controller	= strtolower( $route->controller );
		static::$_registry->action		= strtolower( $route->action );
		$controller						= new $controllerName();
		$controller->setLayout( static::$_layout );
		$controller->queryString		= $route->queryString;

		if( method_exists( $controller, '__init' ) !== false ) {
			
			$controller->__init();
		
		}
			
		if( method_exists( $controller, 'init' ) !== false ) {
			
			$controller->init();
		
		}
		
		if( method_exists( $controller, $actionName ) !== false ) {
			
			$controller->$actionName();
			
			static::$_registry->action	= strtolower( $route->action );
		
		} else {
			
			if( method_exists( $controller, 'IndexAction' ) !== false ) {
			
				$controller->IndexAction();

				static::$_registry->action	= 'index';
				$route->action				= 'index';
			
			} else {
				
				throw new GAS_Controller_Front_Exception( 'Malformed/Invalid controller.' );
			
			}
			
		}
		
		if( method_exists( $controller, 'destruct' ) !== false ) {
		
			$controller->destruct();
		
		}
				
		self::$_controller = $controller;

		$view = $controller->layout->getView();
		
		if( $view->getView() !== null ) {
			
			$view->render();
		
		} else {
		
			$view->render( strtolower( str_replace( '_', '/', $route->controller ) ) . '/' . ucwords( $route->action ) );
			
		}
	
		return $controller;

	}
	
	private static function _prepare() {
	
		static::$_definitions = new GAS_Config( array(
		
			'controllerDirectory'	=> '_application/controllers/',
			'baseUrl'				=> '/',
			'defaultController'		=> 'Index'
		
		), true );
		
	}

}