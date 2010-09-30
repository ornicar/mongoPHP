<?php

class GAS_Controller_Router extends GAS_Controller {

	protected $_route				= array();
	protected static $_routes		= array();
	protected static $_queries		= array();
	protected static $_definitions;
	
	public function __construct( GAS_Config $definitions = null ) {
		
		self::$_definitions	= $definitions;
	
	}
	
	public function route() {
			
		$this->getRoute();
		$this->checkRoutes();
		
		return $this;

	}
	
	public function returnRoute() {
	
		return $this->_route;
	
	}
		
	public function addRoute( $route, $do, $queryString = array() ) {
	
		self::$_routes[ $route ]	= $do;
		self::$_queries[ $route ]	= $queryString;
		
		return $this;
	
	}
	
	public function getRoute() {
		
		$route	= new stdClass();
		$uri	= urldecode( $_SERVER[ 'REQUEST_URI' ] );
		$uri	= explode( '?' . urldecode( $_SERVER[ 'QUERY_STRING' ] ), $uri );
		$uri	= $uri[ 0 ];
		$uri	= str_replace( '//', '/', $uri );
		$base	= self::$_definitions->baseUrl;

		if( is_string( $base ) !== false && $base != '/' ) {
			
			$uri	= explode( $base, $uri );

			unset( $uri[ 0 ] );

			$uri	= implode( '/', $uri );
			$uri	= explode( '/', $uri );

		} else {
			
			$uri	= explode( '/', $uri );
			
			if( $uri[ 0 ] == '' ) {
				
				unset( $uri[ 0 ] );
				
				$array = array();
				
				foreach( $uri as $val ) {
				
					$array[] = $val;
				
				}
				
				$uri = $array;
				
			}
		
		}

		if( isset( $uri[ 0 ] ) !== false ) {
			
			$uri[ 0 ] = trim( $uri[ 0 ] );
			
			if( empty( $uri[ 0 ] ) !== true && $uri[ 0 ] != '' ) {
				
				$controller			= str_replace( '-', ' ', $uri[ 0 ] );
				$controller			= ucwords( $controller );
				$controller			= str_replace( ' ', '', $controller );
				$route->controller	= $controller;
			
			} else {
			
				$route->controller	= self::$_definitions->defaultController;

			}
			
			unset( $uri[ 0 ] );

		} else {
		
			$route->controller	= self::$_definitions->defaultController;
		
		}
		
		if( isset( $uri[ 1 ] ) !== false ) {
			
			$uri[ 1 ] = trim( $uri[ 1 ] );
			
			if( empty( $uri[ 1 ] ) !== true && $uri[ 1 ] != '' ) {
				
				$uri[ 1 ]		= str_replace( array( '-', '_' ), ' ', $uri[ 1 ] );
				$uri[ 1 ]		= ucwords( $uri[ 1 ] );
				$uri[ 1 ]		= str_replace( ' ', '', $uri[ 1 ] );
				
				$route->action	= $uri[ 1 ];
			
			} else {
			
				$route->action	= 'Index';
			
			}
			
			unset( $uri[ 1 ] );
			
		} else {
		
			$route->action	= 'Index';
		
		}

		// Reset array indexes
		$route->queryString	= array_values( $uri );
		$this->_route		= $route;
	
	}
	
	public function checkRoutes() {

		$uri	= urldecode( $_SERVER[ 'REQUEST_URI' ] );
		$uri	= str_replace( '//', '/', $uri );
		$base	= self::$_definitions->baseUrl;
		
		if( is_string( $base ) !== false ) {
			
			$uri	= explode( $base, $uri );
			
			unset( $uri[ 0 ] );
			
			$uri	= implode( '/', $uri );
			$uri	= explode( '/', $uri );
					
		} else {
			
			$uri	= explode( '/', $uri );
		
		}

		foreach( self::$_routes as $route => $do ) {
		
			$parts			= explode( '/', $route );
			$action			= 'Index';
			$controllerDo	= explode( ':', $do );
			$controller		= $controllerDo[ 0 ];
			$queryString	= array();
			$objects		= array();
			$valid			= true;
			
			if( count( $controllerDo ) > 1 ) {
				
				$action	= $controllerDo[ 1 ];
			
			}
			
			unset( $parts[ 0 ] );
			
			$parts	= array_values( $parts );
			$index	= 0;	

			foreach( $parts as $index => $part ) {
				
				if( substr( $part, 0, 1 ) === ':' && isset( $uri[ $index ] ) !== false ) {
					
					/**
					 * Named parameter
					 */
					$queryString[ substr( $part, 1 ) ] = $uri[ $index ];
				
				} else if( $part === '[action]' ) {
					
					/**
					 * The action is actually this part of the URI
					 */
					if( isset( $uri[ $index ] ) !== false && $uri[ $index ] != '' ) {
					
						$action	= str_replace( '-', '_', $uri[ $index ] );
					
					}

				} else if( $part === '[controller]' ) {
					
					/**
					 * The controller is actually this part of the URI
					 */
					if( isset( $uri[ $index ] ) !== false && $uri[ $index ] != '' ) {
					
						$controller	= str_replace( '-', '_', $uri[ $index ] );
					
					}
								
				} else if( isset( $uri[ $index ] ) !== true || $part != $uri[ $index ] ) {
					
					$valid = false;
				
				}
		
			}
						
			$index++;
			
			for( $index; $index < count( $uri ); $index++ ) {
				
				$queryString[] = $uri[ $index ];
			
			}
			
			if( isset( self::$_queries[ $route ] ) !== false ) {
				
				$queryString = array_merge( $queryString, self::$_queries[ $route ] );
			
			}
						
			if( $valid === true && ( $index >= count( $uri ) || $uri[ $index ] == '' ) ) {
			
				$this->_route = (object)array(
					
					'controller'	=> $controller,
					'action'		=> $action,
					'queryString'	=> $queryString
				
				);
			
			}
						
		}
			
	}

}

?>