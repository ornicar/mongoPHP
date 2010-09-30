<?php

class GAS_Http_Sockets_Request extends GAS_Http {

	protected $_connection;
	protected $_uri 	= null;
	protected $_port	= 80;
	protected $_cookies	= null;
	protected $_post	= array();
	protected $_get		= array();
	protected $_headers	= array();
	
	public function __construct( $uri = null, $port = 80 ) {
		
		if( function_exists( 'fsockopen' ) !== false ) {
			
			if( $uri !== null ) {
			
				$this->setUri( $uri );
					
			}
			
			if( $port !== null ) {
				
				$this->setPort( $port );
			
			}
			
			$this->setUserAgent( 'Mozilla/5.0 (X11; U; Linux i686; pl-PL; rv:1.9.0.2) Gecko/20121223 Ubuntu/9.25 (jaunty) Firefox/3.8' );
			$this->setContentType( 'application/x-www-form-urlencoded' );
			
		} else {
			
			throw new GAS_Http_Curl_Exception( 'The socket extension is required to use this class.' );
		
		}
			
	}
	
	public function setUri( $uri ) {
	
		$this->_uri	= $uri;
				
		return $this;
	
	}
	
	public function setPort( $port ) {
	
		$this->_port = $port;
				
		return $this;
	
	}
	
	public function getUri() {
		
		return $this->_uri;
	
	}
	
	public function getPort() {
		
		return $this->_port;
	
	}
	
	public function setHeader( $header, $value ) {
		
		$header	= GAS_Http::formatHeader( $header );
		
		$this->_headers[ $header ] = $value;
		
		return $this;
	
	}
	
	public function setUserAgent( $agent ) {
		
		$this->setHeader( 'User-Agent', $agent );
		
		return $this;
		
	}
		
	public function setAuth( $username, $password ) {
	
		$this->setCurlOpt( CURLOPT_USERPWD, $username . ':' . $password );
		
		return $this;
		
	}
	
	public function setContentType( $type ) {
	
		$this->setHeader( 'Content-Type', $type );
		
		return $this;
		
	}
	
	public function addPost( $key, $value ) {
	
		$this->_post[ $key ] = $value;
	
	}
	
	public function addGet( $key, $value ) {
	
		$this->_get[ $key ] = $value;
	
	}
	
	public function sendRequest( $headers = null ) {
	
		$uri	= $this->_uri;
		$method	= 'GET';
		
		if( count( $this->_get ) > 0 ) {
			
			if( strpos( $uri, '?' ) === false ) {
			
				$uri .= '?';
			
			}
		
			$uri .= http_build_query( $this->_get );
		
		}
				
		if( $this->_connection = fsockopen( $this->_uri, $this->_port, $errorNumber, $errorString ) ) {
	
			if( count( $this->_post ) > 0 ) {
				
				$method = 'POST';
				$post	= http_build_query( $this->_post );
				
				$this->setHeader( 'Content-Length', strlen( $post ) );
			
			}
			
			$explode	= explode( '/', $this->_uri );
			$host		= $explode[ 0 ];

			unset( $explode[ 0 ] );
			
			$path		 = implode( '/', $explode[ 1 ] );
			$request	 = $method . ' ' . $path . ' HTTP/1.1' . "\r\n";
			$request	.= 'Host: ' . $host . "\r\n";
			
			foreach( $this->_headers as $header => $value ) {
				
				$request .= $header . ': ' . $value . "\r\n";
							
			}
			
			$request	.= 'Connection: close' . "\r\n";
			
			if( $method === 'POST' ) {
			
				$request	.= $post;
			
			}
			
			fwrite( $this->_connection, $request );
		
			$responseHeader		= '';
			$responseContent	= '';
		
			do {
			
				$responseHeader	.= fread( $this->_connection, 1 ); 
	        
			} while( !preg_match( '/\\r\\n\\r\\n$/', $responseHeader ) );
	        
			if( !strstr( $responseHeader, 'Transfer-Encoding: chunked' ) ) {
	        
				while( !feof( $this->_connection ) ) {
				
	                $responseContent	.= fgets( $this->_connection, 128 );
	            
	            }
	        
			} else {
	
	            while( $chunk_length = hexdec( fgets( $this->_connection ) ) ) {
	            
					$responseContentChunk	= '';
					$read_length			= 0;
	                
					while( $read_length < $chunk_length ) {
	                
	                    $responseContentChunk	.= fread( $this->_connection, ( $chunk_length - $read_length ) );
	                    $read_length			 = strlen( $responseContentChunk );
	                }
	
	                $responseContent	.= $responseContentChunk;
	                
	                fgets( $this->_connection );
	                
	            }
	            
	        }
	        
	        if( $headers !== null ) {
	        	
	        	return array(
	        	
	        		'html'		=> $responseContent,
	        		'headers'	=> $repsonseHeaders
	        	
	        	);
	        
	        } else {
	        
	        	return $responseContent;
	        
	        }
		
		} else {
			
			throw new GAS_Http_Socket_Exception( 'Failed opening HTTP socket connection to ' . $this->_uri . ' on port ' . $this->_port . '. The server said: ' . $errorString . ' (code: ' . $errorNumber . ')' );
		
		}
				
	}
	
}