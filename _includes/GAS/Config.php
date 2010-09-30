<?php
/**
 * GAS_Config
 *
 * A class that is used to contain a configuration file/object.
 *
 * @category	GAS_Config
 * @package		GAS_Config
 * @author		Simon Fletcher
 * @version		SVN: $Id$
 * @since		v0.1 BETA
 * @extends		GAS
 * @implements	Countable, Iterator
 */
class GAS_Config extends GAS implements Countable, Iterator {

	/**
	 * _path
	 * 
	 * (default value: null)
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $_path = null;
	
	/**
	 * _rootNode
	 * 
	 * (default value: null)
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $_rootNode = null;
	
	/**
	 * _dataSource
	 * 
	 * (default value: array())
	 * 
	 * @var array
	 * @access protected
	 */
	protected $_dataSource = array();
	
	/**
	 * _allowModifications
	 * 
	 * (default value: false)
	 * 
	 * @var bool
	 * @access protected
	 */
	protected $_allowModifications = false;
	
	/**
	 * _index
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $_index = 0;
	
	/**
	 * _count
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $_count = 0;
	
	/**
	 * _skipNextIteration
	 * 
	 * (default value: false)
	 * 
	 * @var bool
	 * @access protected
	 */
	protected $_skipNextIteration = false;
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @param array Array $dataSource. (default: array())
	 * @param bool $allowModifications. (default: false)
	 * @return void
	 */
	public function __construct( Array $dataSource = array(), $allowModifications = false ) {
		
		$this->_allowModifications	= (bool)$allowModifications;

		foreach( $dataSource as $key => $value ) {
				
			if( is_array( $value ) !== false && count( $value ) > 1 ) {
				
 				$this->_dataSource[ $key ] = new self( $value, $this->_allowModifications );

			} else {
				
				$this->_dataSource[ $key ] = $value;
			
			}
		
		}		

		$this->_count = count( $this->_dataSource );
		
	}
	
	/**
	 * _setObserver function.
	 * 
	 * @access private
	 * @param mixed $key
	 * @param mixed $value
	 * @param mixed $realValue
	 * @return void
	 */
	private function _setObserver( $key, $value, $realValue ) {
		/**
		 * Allows override for saving, etc
		 */
	}

	/**
	 * toArray function.
	 * 
	 * @access public
	 * @return array
	 */
	public function toArray() {
    
		$array	= array();
        $data	= $this->_dataSource;
        
        foreach( $data as $key => $value ) {
	        
			if( $value instanceof GAS_Config ) {
		
				$array[ $key ] = $value->toArray();
		
			} else {
	
				$array[ $key ] = $value;
	
			}

		}

		return $array;

	}
	
	/**
	 * __get function.
	 * 
	 * @access public
	 * @param mixed $key
	 * @return string
	 */
	public function __get( $key ) {

		if( array_key_exists( $key, $this->_dataSource ) ) {
		
			return $this->_dataSource[ $key ];
		
		} else {
			
			return '';
		
		}
	
	}
	
	/**
	 * __set function.
	 * 
	 * @access public
	 * @param mixed $key
	 * @param mixed $value
	 * @return void
	 * @throws GAS_Config_Exception
	 */
	public function __set( $key, $value ) {
		
		if( $this->_allowModifications === true ) {
						
			if( is_array( $value ) !== false ) {
			
 				$this->_dataSource[ $key ] = new self( $value, $this->_allowModifications );

			} else {
				
				$this->_dataSource[ $key ] = $value;
			
			}
			
			$this->_count = count( $this->_dataSource );
							
			$this->_setObserver( $key, $value, $this->_dataSource[ $key ] );
		
		} else {
		
			throw new GAS_Config_Exception( 'Read only mode.' );
		
		}
	
	}
	
	/**
	 * __isset function.
	 * 
	 * @access public
	 * @param mixed $key
	 * @return bool
	 */
	public function __isset( $key ) {
		
		if( array_key_exists( $key, $this->_dataSource ) ) {
			
			return true;
		
		} else {
			
			return false;
		
		}
			
	}

	/**
	 * count function.
	 * 
	 * @access public
	 * @return int
	 */
	public function count() {
	
		return $this->_count;
	
	}

	/**
	 * current function.
	 * 
	 * @access public
	 * @return mixed
	 */
	public function current() {
	
		$this->_skipNextIteration = false;
		
		return current( $this->_dataSource );
	
	}

	/**
	 * key function.
	 * 
	 * @access public
	 * @return mixed
	 */
	public function key() {
	
		return key( $this->_dataSource );
	
	}

	/**
	 * next function.
	 * 
	 * @access public
	 * @return mixed
	 */
	public function next() {
	
		if( $this->_skipNextIteration === true ) {
		
			$this->_skipNextIteration = false;
			
			return;
		
		}
		
		next( $this->_dataSource );
		
		$this->_index++;
	
	}

	/**
	 * rewind function.
	 * 
	 * @access public
	 * @return mixed
	 */
	public function rewind() {
	
		$this->_skipNextIteration = false;
	
		reset( $this->_dataSource );
	
		$this->_index = 0;
	
	}
	
	/**
	 * valid function.
	 * 
	 * @access public
	 * @return bool
	 */
	public function valid() {
	
		return ( $this->_index < $this->_count );
	
	}

}