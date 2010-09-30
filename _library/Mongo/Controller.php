<?php

class Mongo_Controller extends GAS_Controller_Abstract {

	public $mongo;
	public static $config;
	
	public function __init() {
	
		if( class_exists( 'Mongo' ) !== true ) {
			
			throw new GAS_Exception( 'The Mongo PHP extension is required to use this panel.' );
		
		}
		
		self::$config = GAS_Bootstrap::getConfig();
		
		define( 'SITE_ROOT', self::$config->application->path );
			
		$this->_initMongo();
		$this->_initLayout();
		$this->_initView();
	
	}
	
	public function auth() {
		
		return GAS_Bootstrap::$session;
	
	}
	
	public function setVar( $key, $value ) {
		
		$this->view->$key = $this->layout->$key = $value;
		
		return $this;
	
	}

	private function _initMongo() {
	
		$this->mongo = new Mongo( self::$config->mongo->address );
		
		if( isset( $this->auth()->databaseName ) && $this->auth()->databaseName !== false ) {
			
			$database		= $this->mongo->selectDB( $this->auth()->databaseName );
			$this->database =& $database;
			
			$this->setVar( 'inDatabase', true );
			$this->setVar( 'databaseName', $this->auth()->databaseName );
			$this->setVar( 'collections', $database->listCollections() );
			
			if( isset( $this->auth()->collectionName ) !== false && $this->auth()->collectionName !== false ) {
			
				$this->collection = $database->selectCollection( $this->auth()->collectionName );
				$this->setVar( 'collectionName', $this->auth()->collectionName );
				
			}
			
		} else {
			
			$this->setVar( 'inDatabase', false );
		
		}
				
	}
	
	private function _initLayout() {

		$this->layout->databases	= $this->mongo->listDBs();
	
	}
	
	private function _initView() {
		
		$this->view->js			= array();
		$this->view->jsAdded	= array(
			
			'before'	=> array(),
			'after'		=> array()
		
		);
	
	}

}