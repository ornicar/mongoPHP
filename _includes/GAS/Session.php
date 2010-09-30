<?php

class GAS_Session extends GAS {

	/**
	 * A prefix for the sessions so you can have sessions
	 * on several parts of your website that won't conflict
	 * @var null|string
	 */
	protected $_prefix	= null;
	protected $_salt	= null;
	
	/**
	 * @method __construct
	 * @param string $prefix
	 */
	public function __construct() {
		
		/**
		 * If session_start() hasn't been called yet and headers haven't been sent, run session_start()
		 */
		if( isset( $_SESSION ) !== true && headers_sent() !== true ) {
		
			/**
			 * Ok, let's enable session writing
			 */
			session_start();
		
		}
	
	}
	
	/**
	 * @method Sessions __set() __set(string $key, string $value) Sets a session
	 * @param string $key
	 * @param string $value
	 * @return Sessions
	 */
	public function __set( $key, $value = null ) {

		/**
		 * Call and return the setSession method
		 */		
		return $this->setSession( $key, $value );
	
	}

	/**
	 * @method string __get() __get(string $key) Gets a session by its index
	 * @param string $key
	 * @return string
	 */	
	public function __get( $key ) {

		/**
		 * Call and return the getSession method
		 */			
		return $this->getSession( $key );
	
	}
	
	public function __isset( $key ) {
	
		return isset( $_SESSION[ $this->_prefix . $key ] );
	
	}
	
	/**
	 * @method Sessions setSession() setSession(string $key, string $value) Sets a session
	 * @param string $key
	 * @param string $value
	 * @return Sessions
	 */	
	public function setSession( $key, $value = null ) {
	
		if( is_array( $key ) !== false ) {
		
			foreach( $key as $index => $_value ) {
			
				$_SESSION[ $this->_prefix . $index ] = $_value;
			
			}
		
		} else {
			
			/**
			 * Set the session
			 */
			$_SESSION[ $this->_prefix . $key ] = $value;
		
		}
		
		/**
		 * Return this instance to allow method chaining
		 */
		return $this;
	
	}

	/**
	 * @method string getSession() getSession(string $key) Gets a session by its index
	 * @param string $key
	 * @return string
	 */		
	public function getSession( $key ) {
		
		if( array_key_exists( $this->_prefix . $key, $_SESSION ) !== false ) {
		
			return $_SESSION[ $this->_prefix . $key ];
		
		}
		
		return false;
		
	}

	/**
	 * @method Sessions logout() logout() Clears all sessions with the instance's prefix
	 * @return Sessions
	 */	
	public function clear() {
	
		/**
		 * Loop through the $_SESSION array
		 */
		foreach( $_SESSION as $index => $value ) {
			
			/**
			 * If the prefix is found at the start of the string, then unset the session
			 */
			if( preg_match( '/^' . $this->_prefix . '(.*?)$/is', $index ) === 1 ) {
				
				/**
				 * Unset (remove) that session
				 */
				unset( $_SESSION[ $index ] );
			
			}
		
		}
		
		/**
		 * Return this instance to allow method chaining
		 */
		return $this;
			
	}
	
	public function setSalt( $salt ) {
		
		$this->_salt = $salt;
		
		return $this;
	
	}
	
	public function encrypt( $string ) {
		
		$string = sha1( $this->_salt . $string );
		
		return $string;
	
	}

}