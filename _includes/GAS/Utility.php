<?php

class GAS_Utility extends GAS {
	
	public static function clean( $contents, $uri = false ) {
		
		if( is_array( $contents ) !== false ) {
		
			foreach( $contents as $key => $content ) {
				
				unset( $contents[ $key ] );
				
				$key				= str_replace( array( chr( 0 ), '$' ), '', $key );
				$contents[ $key ]	= static::clean( $content );
			
			}
			
			return $contents;
		
		} else {
			
			if( $contents != '' ) {
				
				if( $uri !== false ) {
				
					$contents = urldecode( $contents );
				
				}
				
				$contents	= stripslashes( $contents );
				$contents	= htmlspecialchars( $contents );
				//$contents	= htmlentities( $contents, ENT_QUOTES );
										
			}
						
			return $contents;
		
		}
			
	}
	
	public static function redirect( $uri ) {
		
		if( headers_sent() !== true ) {
			
			header( 'Location: ' . $uri );
		
		} else {
		
			echo '<script type="text/javascript">window.location.href=\'' . $uri . '\';</script>';
		
		}
		
		exit;
	
	}

	public static function validEmail( $email ) {
			
		//if( function_exists( 'filter_var' ) !== false ) {
		
		//	return filter_var( $email, FILTER_VALIDATE_EMAIL );
		
		//} else {
			
			if( ( preg_match( '/(@.*@)|(\.\.)|(@\.)|(\.@)|(^\.)/', $email ) ) || ( preg_match( '/^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})(\]?)$/', $email ) ) ) {
				
				$host = explode( '@', $email);
	
				if( isset( $host[ 1 ] ) !== true ) {
				
					return false;
					
				}
				
				if( isset( $host[ 1 ] ) !== false && self::_checkdnsrr( $host[ 1 ] . '.', 'MX' ) ) {
				
					return true;
				
				}
				
				if( isset( $host[ 1 ] ) !== false && self::_checkdnsrr( $host[ 1 ] . '.', 'A' ) ) {
				
					return true;
				
				}
				
				if( isset( $host[ 1 ] ) !== false && self::_checkdnsrr( $host[ 1 ] . '.', 'CNAME' ) ) {
				
					return true;
				
				}
				
			}
			
			return false;
		
		//}
				
	}
	
	public static function _checkdnsrr( $host, $type = '' ) {
 	
		if( !function_exists( 'checkdnsrr' ) ) {
		
			if( !empty( $host ) ) {
				
				if( $type == '' ) {
				
					$type = 'MX';
				
				}
				
				@exec( 'nslookup -type=' . $type . $host, $output );
				
				while( list( $k, $line ) = each( $output ) ) {

					if( eregi( '^' . $host, $line ) ) {

						return true;

					}

				}

				return false;

			}
			
		} else {
		
			return checkdnsrr( $host, $type );
		
		}
		
	}

	public static function multidimensionalSort( $array, $id = 'id', $sort_ascending = true ) {
	
		$temp_array = array();
		
		while( count( $array ) > 0 ) {
	
			$lowest_id	= 0;
			$index		= 0;
			
			foreach( $array as $item ) {
					
				if( isset( $item[ $id ] ) !== false ) {
				
					if( $array[ $lowest_id ][ $id ] ) {
					
						if( strtolower( $item[ $id ] ) < strtolower( $array[ $lowest_id ][ $id ] ) ) {
						
							$lowest_id = $index;
				
						}
					
					}
					
				}
				
				$index++;
		
			}
			
			$temp_array[]	= $array[ $lowest_id ];
			$array			= array_merge( array_slice( $array, 0, $lowest_id ), array_slice( $array, ( $lowest_id + 1 ) ) );
		
		}
		
		if( $sort_ascending ) {
		
			return $temp_array;
	
		} else {
	
			return array_reverse( $temp_array );
	
		}

	}	
	
}