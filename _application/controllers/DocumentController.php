<?php

class DocumentController extends Mongo_Controller {

	public function addAction() {

		if( isset( $this->_post[ 'document' ] ) !== false ) {
			
			eval( '$object = ' . $_POST[ 'document' ] . ';' );
			$this->collection->save( $object );	
			
			GAS_Utility::redirect( SITE_ROOT . 'collection/get/' . $this->collection->getName() . '#' . $object[ '_id' ] );
					
		}
		
		exit;
	
	}
	
	public function editAction() {
	
		$id = $this->queryString[ 0 ];
		
		if( $document = $this->collection->findOne( array( '_id' => new MongoId( $id ) ) ) ) {
			
			if( isset( $this->_post[ 'code' ] ) !== false ) {
				
				eval( '$object = ' . $_POST[ 'code' ] . ';' );
				$this->collection->save( $object );	
				
				GAS_Utility::redirect( SITE_ROOT . 'collection/get/' . $this->collection->getName() . '#' . $id );
						
			}
			
			if( GAS_Http::isAjax() === true ) {
			
				echo Mongo_Utility::outputForEdit( $document );
				exit;
			
			} else {
				
				$this->view->document	= $document;	
				$this->view->code		= Mongo_Utility::outputForEdit( $document );
			
			}
	
		} else {
			
			exit;
		
		}
			
	}

	public function removeAction() {
	
		$id = $this->queryString[ 0 ];
		
		if( $document = $this->collection->findOne( array( '_id' => new MongoId( $id ) ) ) ) {
			
			$this->collection->remove( array( '_id' => new MongoId( $id ) ) );

			GAS_Utility::redirect( SITE_ROOT . 'collection/get/' . $this->collection->getName() );
				
		} else {
			
			exit;
		
		}
			
	}
	
}