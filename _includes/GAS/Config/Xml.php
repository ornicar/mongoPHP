<?php
/**
 * GAS_Config_XML
 *
 * Parses an .xml file and creates a GAS_Config instance.
 *
 * @category	GAS_Config
 * @package		GAS_Config
 * @author		Simon Fletcher
 * @version		SVN: $Id$
 * @since		v0.1 BETA
 * @extends		GAS_Config
 */
class GAS_Config_Xml extends GAS_Config {
	
	/**
	 * _simpleXml
	 * 
	 * (default value: null)
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $_simpleXml = null;
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @param mixed $iniPath
	 * @param mixed $rootNode. (default: null)
	 * @param bool $readOnly. (default: false)
	 * @return void
	 */
	public function __construct( $iniPath, $rootNode = null, $readOnly = false, $file = true ) {
		
		$this->_path		= $iniPath;
		$this->_rootNode	= $rootNode;

		$this->_parse( $file );
		
		parent::__construct( $this->_simpleXml, (bool)$readOnly );
	
	}
	
	/**
	 * _parse function.
	 * 
	 * @access protected
	 * @return void
	 * @throws GAS_Config_Exception
	 */
	protected function _parse( $file ) {
		
		if( $file === false || file_exists( $this->_path ) !== false ) {
				
			try {
				
				$this->_simpleXml	= new SimpleXMLElement( $this->_path, LIBXML_NOCDATA, $file );			
				$this->_simpleXml	= $this->_cleanup( $this->_simpleXml );

				if( $this->_rootNode !== null ) {
				
					$this->_simpleXml = $this->_locateRoot( $this->_simpleXml );
								
				}
				
			} catch( Exception $e ) {
			
				throw new GAS_Config_Exception( 'Malformed configuration file given.' );
			
			}
		
		} else {
		
			throw new GAS_Config_Exception( 'Invalid configuration file given.' );
		
		}
	
	}
	
	/**
	 * _cleanup function.
	 * 
	 * @access protected
	 * @param mixed SimpleXMLElement $xml
	 * @return SimpleXMLElement
	 */
	protected function _cleanup( SimpleXMLElement $xml ) {
		
		$children	= (array)$xml->children();
		
		if( count($children) === 0 ) {
		
			return '';
		
		} else {
					
			if( is_array( $children ) !== false && count( $children ) > 0 ) {
				
				foreach( $children as $key => $value ) {
					
					if( ( $value instanceof SimpleXMLElement ) !== false ) {
					
						$children[ $key ]	= $this->_cleanup( $value );
						
						if( $children[ $key ] != '' ) {
						
							$children[ $key ][ 'attributes' ]	= $this->_cleanAttributes( $value->attributes() );
						
						}
						
					} else {
						
						if( is_array( $value ) !== false ) {
						
							$children[ $key ] = $this->_cleanArray( $value );
						
						} else {
						
							$children[ $key ] = (string) $value;
						
						}
						
					}
							
				}
			
			} else {
				
				$children = '';
			
			}
				
			return $children;
		
		}
			
	}
	
	/**
	 * _cleanArray function.
	 * 
	 * @access protected
	 * @param mixed Array $array
	 * @return array
	 */
	protected function _cleanArray( Array $array ) {
				
		foreach( $array as $int => $v ) {
					
			if( is_array( $v ) !== false ) {
				
				$array[ $int ] = $this->_cleanArray( $v );
			
			} else {
			
				if( ( $v instanceof SimpleXMLElement ) !== false ) {
									
					$array[ $int ] = $this->_cleanup( $v );
					
				} else {
					
					$array[ $int ] = $v;
				
				}
			
			}
		
		}

		return $array;
	
	}
	
	/**
	 * _locateRoot function.
	 * 
	 * @access protected
	 * @param mixed Array $array
	 * @return mixed
	 */
	protected function _locateRoot( Array $array ) {
	
		foreach( $array as $key => $value ) {
		
			if( $key === $this->_rootNode ) {
			
				return $array[ $key ];
			
			} else {
			
				if( is_array( $value ) !== false ) {
					
					if( $check = $this->_locateRoot( $value ) ) {
						
						return $check;
					
					}
				
				}
			
			}
		
		}
		
		return false;
	
	}

	/**
	 * _toObject function.
	 * 
	 * @access protected
	 * @param mixed $data
	 * @return object
	 */
	protected function _toObject( $data ) {

		if( is_array( $data ) !== true ) {
		
			return $data;
		
		}
		
		$object = new stdClass();
		
		if( is_array( $data ) !== false && count( $data ) > 0 ) {
		
			foreach( $data as $name => $value ) {
							
				if( empty( $name ) !== true ) {
				
					$object->$name = $this->_toObject( $value );
				
				}
				
			}
			
		}
	
		return $object;
	
	}
	
	protected function _cleanAttributes( $data ) {
	 	
	 	$return = array();
	 	
		foreach( $data as $int => $item ) {
		
			$return[ $int ] = (string)$item;
		
		}
		
		return $return;
	 
	}

}