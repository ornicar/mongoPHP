<?php
/**
 * GAS_Config_Ini
 *
 * Parses an .ini file and creates a GAS_Config instance.
 *
 * @category	GAS_Config
 * @package		GAS_Config
 * @author		Simon Fletcher
 * @version		SVN: $Id$
 * @since		v0.1 BETA
 * @extends		GAS_Config
 */
class GAS_Config_Ini extends GAS_Config {

	/**
	 * _iniFile
	 * 
	 * (default value: null)
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $_iniFile = null;
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @param mixed $iniPath
	 * @param mixed $rootNode. (default: null)
	 * @return void
	 */
	public function __construct( $iniPath, $rootNode = null ) {
	
		$this->_path		= $iniPath;
		$this->_rootNote	= $rootNode;
	
	}
	
	/**
	 * _parse function.
	 * 
	 * @access protected
	 * @return void
	 */
	protected function _parse() {
	
		if( file_exists( $this->_path ) !== false ) {
		
			$this->_iniFile = parse_ini_file( $this->_path, true );
					
		} else {
		
			throw new GAS_Config_Exception( 'Invalid configuration file given.' );
		
		}
	
	}

}