<?php

class DatabaseController extends Mongo_Controller {

	public function addAction() {
		
		if( array_key_exists( 'databaseName', $this->_post ) !== false ) {
		
			$this->mongo->selectDb( $this->_post[ 'databaseName' ] );
			
			GAS_Utility::redirect( SITE_ROOT . 'database/set/' . $this->_post[ 'databaseName' ] );
			
		}
		
		exit;
	
	}
	
	public function setAction() {
		
		$databaseName					= $this->queryString[ 0 ];
		$database						= $this->mongo->selectDB( $databaseName );
		$this->auth()->databaseName		= $databaseName;
		$this->auth()->collectionName	= false;
			
		$this->setVar( 'inDatabase', true );
		$this->setVar( 'databaseName', $databaseName );
		$this->setVar( 'collections', $database->listCollections() );
		
	}
	
	public function dropAction() {
	
		$databaseName					= $this->queryString[ 0 ];
		$database						= $this->mongo->selectDB( $databaseName );
		$database->drop();
		
		$this->auth()->databaseName		= false;
		$this->auth()->collectionName	= false;
		
		GAS_Utility::redirect( SITE_ROOT );
			
	}
	
	public function repairAction() {
	
		$databaseName					= $this->queryString[ 0 ];
		$database						= $this->mongo->selectDB( $databaseName );
		$database->repair();
		
		GAS_Utility::redirect( SITE_ROOT . 'database/set/' . $databaseName );
			
	}

}