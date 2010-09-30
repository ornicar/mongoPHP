<?php

class GAS_Http_Curl_Request extends GAS {

	protected $_curl;
	protected $_uri 	= null;
	protected $_cookies	= null;
	protected $_post	= array();
	protected $_get		= array();
	protected $_curlOpt = array();
	protected $_headers	= array();
	
	public function __construct( $uri = null, $cookies = true ) {
		
		if( function_exists( 'curl_init' ) !== false ) {
			
			$this->_curl = curl_init();
		
			if( $uri !== null ) {
			
				$this->setUri( $uri );
			
			}
			
			$this->setUserAgent( 'Mozilla/5.0 (X11; U; Linux i686; pl-PL; rv:1.9.0.2) Gecko/20121223 Ubuntu/9.25 (jaunty) Firefox/3.8' );
			$this->setSslVerify( false );
			$this->setReturnTransfer( true );
			$this->setFollowLocation( true );
			
			if( $cookies === true ) {
			
				$this->setCurlOpt( 'CURLOPT_COOKIEFILE', $this->_cookies );
				$this->setCurlOpt( 'CURLOPT_COOKIEJAR', $this->_cookies );
			
			}
	
		} else {
			
			throw new GAS_Http_Curl_Exception( 'The cURL extension is required to use this class.' );
		
		}
			
	}
	
	public static function factory( $uri = null, $cookies = true ) {
	
		return new self( $uri, $cookies );
	
	}
	
	public function setUri( $uri ) {
	
		$this->_uri = $uri;
				
		return $this;
	
	}
	
	public function getUri() {
		
		return $this->_uri;
	
	}
	
	public function setHeader( $header, $value ) {
		
		$this->_headers[ $header ] = $value;
		
		return $this;
	
	}
	
	public function setFollowLocation( $bool ) {
		
		$this->setCurlOpt( 'CURLOPT_FOLLOWLOCATION', $bool );
		
		return $this;
	
	}
	
	public function setReturnTransfer( $bool ) {
	
		$this->setCurlOpt( 'CURLOPT_RETURNTRANSFER', $bool );
		
		return $this;
		
	}
	
	public function setSslVerify( $bool ) {
	
		$this->setCurlOpt( 'CURLOPT_SSL_VERIFYPEER', $bool );
		
		return $this;
		
	}
	
	public function setUserAgent( $agent ) {
		
		$this->setCurlOpt( 'CURLOPT_USERAGENT', $agent );
		
		return $this;
		
	}
		
	public function setAuth( $username, $password ) {
	
		$this->setCurlOpt( 'CURLOPT_USERPWD', $username . ':' . $password );
		
		return $this;
		
	}
	
	public function addPost( $key, $value ) {
	
		$this->_post[ $key ] = $value;
		
		return $this;
	
	}
	
	public function addGet( $key, $value ) {
	
		$this->_get[ $key ] = $value;
	
		return $this;
		
	}
	
	public function setPost( $data ) {
	
		$this->_post = $data;
	
	}
	
	public function setGet( $data ) {
	
		$this->_get = $data;
	
	}
	
	public function clearPost() {
		
		$this->_post = array();
		
		return $this;
	
	}

	public function clearGet() {
		
		$this->_get = array();
		
		return $this;
	
	}
	
	public function getPost() {
		
		return $this->_post;	
	}
	
	public function getGet() {
		
		return $this->_get;	
	}

	public function setCurlOpt( $constant, $value ) {
	
		return $this->_curlOpt[ $constant ] = $value;
	
	}
	
	public function sendRequest( $headers = null, $constant = null, $infoConstant = null ) {
	
		$uri	= $this->_uri;
		
		if( count( $this->_get ) > 0 ) {
			
			if( strpos( $uri, '?' ) === false ) {
			
				$uri .= '?';
			
			}
		
			$uri .= http_build_query( $this->_get );
		
		}
				
		curl_setopt( $this->_curl, CURLOPT_URL, $uri );
		
		if( ( is_array( $this->_post ) !== false && count( $this->_post ) > 0 ) || ( is_array( $this->_post ) !== true && $this->_post != '' ) ) {
						
			$this->setCurlOpt( 'CURLOPT_POST', true );
			$this->setCurlOpt( 'CURLOPT_POSTFIELDS', $this->_post );
		
		}
		
		if( count( $this->_headers ) > 0 ) {
		
			$this->setCurlOpt( 'CURLOPT_HTTPHEADER', $this->_headers );
		
		}
		
		foreach( $this->_curlOpt as $constant => $value ) {

			curl_setopt( $this->_curl, constant( $constant ), $value );
		
		}
		
		$response	= curl_exec( $this->_curl );

		if( $headers !== null ) {
		
			$headers = curl_getinfo( $this->_curl );

			return array(
			
				'http'		=> $response,
				'headers'	=> $headers
			
			);
		
		} else {
		
			return $response;
		
		}
		
	}
	
}