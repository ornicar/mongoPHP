<?php

class CollectionController extends Mongo_Controller {

	public function addAction() {
		
		if( array_key_exists( 'collectionName', $this->_post ) !== false ) {
			
			$collection = $this->database->createCollection( $this->_post[ 'collectionName' ] );

			GAS_Utility::redirect( SITE_ROOT . 'collection/get/' . $collection->getName() );
		
		} else {
			
			GAS_Utility::redirect( SITE_ROOT . 'database/set/' . $this->auth()->databaseName );
		
		}
			
	}
	
	public function getAction() {
		
		$collection = $this->queryString[ 0 ];
		$collection	= substr( $collection, strpos( $collection, '.' ) );
		
		if( substr( $collection, 0, 1 ) === '.' ) {
		
			$collection = substr( $collection, 1 );
		
		}
		
		$this->collection	= $this->database->selectCollection( $collection );
		
		if( array_key_exists( 'search', $this->_post ) !== false ) {
			
			$find = array();
			
			switch( substr( $this->_post[ 'searchTerm' ], 0, 1 ) ) {
			
				case '/':
				
					$find[ $_POST[ 'search' ] ] = new MongoRegex( $_POST[ 'searchTerm' ] );
				
					break;
			
				case '{':
					
					$find[ $_POST[ 'search' ] ] = $_POST[ 'searchTerm' ];
				
					break;
					
				case '(': // this part was pretty smart imo
				
					$scalars	= array( 'bool', 'boolean', 'int', 'integer', 'float', 'double', 'string', 'array', 'object', 'null', 'mongoid' );
					if( $close = strpos( $_POST[ 'searchTerm' ] ) ) {
					
						$scalar = strtolower( substr( $_POST[ 'searchTerm' ], 1, ( $close - 1 ) ) );
						
						if( isset( $scalars[ $scalar ] ) !== false ) {
						
							$term = substr( $_POST[ 'searchTerm' ], $close );
							
							if( $cast === 'mongoid' ) {
								
								$term = new MongoId( $cast );
							
							} else {
							
								settype( $term, $cast );
							
							}
							
							$find[ $_POST[ 'search' ] ] = $term;
						
						}
					
					}
					
					break;
			
			}
			
			if( count( $find ) > 0 ) {
				
				$documents	= $this->collection->find( $find );
			
			}
			
		} else if( array_key_exists( 'query', $this->_post ) !== false ) {
	
			if( stripos( $this->_post[ 'query' ], 'array' ) === 0 ) {
				
				eval( '$query = ' . $_POST[ 'query' ] . ';' );
				
			} else {
        
				$query = json_decode( $_POST[ 'query' ], true );
				            
            }

			$documents	= $this->collection->find( $query );
		
		} else {
		
			$documents	= $this->collection->find();
		
		}
		
		if( array_key_exists( 'sort', $this->_post ) !== false ) {
			
			$documents->sort( array( $this->_post[ 'sort' ] => ( $this->_post[ 'order' ] == 'asc' ? 1 : -1 ) ) );
		
		}
		
		$this->setVar( 'indexes', $this->collection->getIndexInfo() );
		
		$keys = array();
		
		foreach( $documents as $doc ) {
			
			$_documents[] = $doc;
			
			foreach( Mongo_Utility::getArrayKeys( $doc ) as $key ) {
				
				if( isset( $keys[ $key ] ) === false ) {
				
					$keys[ $key ] = $key;
				
				}
			
			}
			
		}
				
		$this->setVar( 'documents', $_documents );
		
		$this->auth()->collectionName = $collection;
		
		/*
		$map	= new MongoCode( 'function() {  for (var key in this) { emit(key, null); } }' );
		$reduce	= new MongoCode( 'function( key, stuff ) { return null; }' );
		
		$do		= $this->database->command( array(
		
			'mapreduce'	=> $this->collection->getName(),
			'map'		=> $map,
			'reduce'	=> $reduce,
			'query'		=> array()
		
		));
		
		$x = $this->database->command( array( 'distinct' => $do[ 'result' ], 'key' => '_id' ) );
		
		$this->database->selectCollection( $do[ 'result' ] )->drop();
		*/
		
		$this->setVar( 'fields', $keys );
			
	}
	
	public function removeAction() {
	
		
	
	}
	
	public function indexAction() {
		
		if( array_key_exists( 'index', $this->_post ) ) {
			
			$fields = array();
			
			foreach( $this->_post[ 'index' ] as $key => $value ) {
			
				if( $value != '' ) {
				
					$desc = 1;
					
					if( array_key_exists( 'desc-' . $key, $this->_post ) !== false ) {
						
						if( $this->_post[ 'desc-' . $key ] == 'true' ) {
						
							$desc = -1;
						
						}
					
					}
				
					$fields[ $value ] = $desc;
			
				}
				
			}
			
			$options = array();
			
			if( array_key_exists( 'type', $this->_post ) !== false ) {
			
				if( $this->_post[ 'type' ] == 'unique' ) {
					
					$options[ 'unique' ] = true;
				
				}
			
			}
			
			$this->collection->ensureIndex( $fields, $options );
						
			GAS_Utility::redirect( SITE_ROOT . 'collection/get/' . $this->collection->getName() );
			
		}
		
		exit;
		
	}
	
	public function unindexAction() {
		
		$this->collection->deleteIndex( $this->queryString[ 0 ] );
		
		if( GAS_Http::isAjax() ) {
		
			echo json_encode( array( 'status' => 'ok' ) );
			
			exit;
		
		}
		
		GAS_Utility::redirect( SITE_ROOT . 'collection/get/' . $this->collection->getName() );
	
	}

}