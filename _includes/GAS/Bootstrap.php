<?php

class GAS_Bootstrap {
	
	public static $configPath;
	public static $appPath;
	public static $environment;
	public static $outputCompression;
	public static $frontController;
	public static $db;
	public static $layout;
	public static $session;
	
	protected static $_registry;
	protected static $_config;
	
	public function __construct() {
	}
	
	public static function boot( $configPath, $appPath, $environment = 'production', $mvc = true ) {
	
		static::$configPath		= $configPath;
		static::$appPath		= $appPath;
		static::$environment	= $environment;
		
		static::_getDependencies();
		static::_initRegistry();
		static::_initAutoloader();
		static::_initBootstrap();
		static::_loadConfig();
		static::_loadSessions();
		static::_initEnvironment();
		static::_setPaths();
		
		if( $mvc === true ) {
			
			static::_initFrontController();
			static::_initView();
			
			$response = static::_dispatch();
			
			static::_handeReponse( $response );
		
		}
		
		static::_endBootstrap();
			
	}
	
	protected static function _initBootstrap() {}
	
	protected static function _getDependencies() {
	
		$dependencies	= array(
			
			'GAS.php',
			'GAS/Exception.php',
			'GAS/Registry.php',
			'GAS/Loader.php'
		
		);
		
		foreach( $dependencies as $dependency ) {
			
			if( file_exists( static::$appPath . '_includes/' . $dependency ) !== true ) {
			
				throw new Exception( 'Could not load dependancy: ' . $dependency );
				
			} else {
			
				include_once APP_PATH . '_includes/' . $dependency;
			
			}
		
		}
	
	}
	
	protected static function _initRegistry() {

		static::$_registry = GAS_Registry::getInstance();	
	
	}

	protected static function _initAutoloader() {
	
		static::$_registry->autoloader = GAS_Loader::getInstance();
		
	}
	
	protected static function _loadConfig() {
		
		$type	= substr( static::$configPath, -3 );

		switch( $type ) {
		
			default:
				case 'xml':
					
					static::$_config = new GAS_Config_Xml( static::$configPath, static::$environment, true );
					
					break;
					
			case 'ini':
				
				static::$_config = new GAS_Config_Ini( static::$configPath, static::$environment );
			
				break;
		
		}
		
		static::$_registry->config = static::$_config;
	
	}
	
	protected static function _loadSessions() {
	
		static::$session = GAS_Session_Namespace::factory( static::$_config->session->prefix )
												->setSalt( static::$_config->session->salt );

	}

	protected static function getSession() {

		return static::$session;

	}

	protected static function _initEnvironment() {
	
		$environment	= static::$_config->environment;

		if( $environment->display_errors ) {
			
			@ini_set( 'display_errors', 1 );	
			error_reporting( E_ALL | E_STRICT );

		} else {
		
			error_reporting( 0 );
			@ini_set( 'display_errors', 0 );	
		
		}

		date_default_timezone_set( $environment->default_timezone );
		
        static::$_registry->globals				= new stdClass();
        static::$_registry->globals->unclean	= array(
        	
        	'post'	=> $_POST,
        	'get'	=> $_GET
        
        );

        static::$_registry->globals->clean		= array(
		
			'post'	=> GAS_Utility::clean( $_POST ),
			'get'	=> GAS_Utility::clean( $_GET )
		
		);
					
	}
	
	protected static function _setPaths() {

		static::$_registry->autoloader->paths[ 'controllers' ]	= static::$_config->application->controllers->location;
        static::$_registry->autoloader->paths[ 'views' ]		= static::$_config->application->views->location;
	
	}
	
	protected static function _initFrontController() {
		
		static::$frontController = GAS_Controller_Front::getInstance();
		static::$frontController->setControllerDirectory( static::$_registry->autoloader->paths[ 'controllers' ] )
       							->setViewDirectory( static::$_registry->autoloader->paths[ 'views' ] )
       							->setViewScriptsDirectory( 'scripts/' )
       							->setLayoutDirectory( 'layouts/' )
       							->setBaseUrl( static::$_config->application->path )
	   							->setDefaultControllerName( static::$_config->application->controllers->defaultController )
								->route();
	
	}
	
	protected static function _initView() {

		$layout = new GAS_Layout();
		$view	= new GAS_View();

		$layout->setLayoutPath( static::$frontController->getDefinitions()->viewDirectory . static::$frontController->getDefinitions()->layoutDirectory );
		$layout->setLayout( ( static::$_config->application->views->defaultLayout ) ? static::$_config->application->views->defaultLayout : 'common' );
		$layout->setView( $view );
		
		$layout->site = (object)static::$_config->site;
				
		$view->setViewScriptPath( static::$frontController->getDefinitions()->viewDirectory . static::$frontController->getDefinitions()->viewScriptsDirectory );
		$view->setEncoding( 'UTF-8' );
		
		static::$frontController->attachLayout( $layout );
	
	}
	
	protected static function _dispatch( $controllerName = null, $actionName = null ) {
	
		return static::$frontController->dispatch( $controllerName, $actionName );
	
	}
	
	protected static function _handeReponse( GAS_Controller_Abstract $controller ) {
		
		$controller->sendResponse();
	
	}
	
	protected static function _endBootstrap() {}
	
	public static function getConfig() {
		
		return self::$_config;
	
	}
	
}