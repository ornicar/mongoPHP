<?php

class Mongo_Utility {
	
	const DRILL_DOWN_DEPTH_LIMIT = 8;
	
	public static function getIds( $data, $type ) {
		
		$ids = array();
		
		foreach( $data as $key => $value ) {
		
			if( is_array( $value ) !== false ) {
				
				$ids = array_merge( $ids, self::getIds( $value, $type ) );
			
			} else if( ( $value instanceof $type ) === true ) {
					
				$ids[] = $value->__toString();
			
			}
		
		}
		
		return $ids;
	
	}
	
	public static function output( $data ) {
		
		$ids = self::getIds( $data, 'MongoId' );
		
		ob_start();
		
		var_dump( $data );
		
		$dump = ob_get_clean();
		
		preg_match_all( '/object\(MongoId\)\#([0-9]+) \(0\) \{(.*?)\}/is', $dump, $matches );
		
		foreach( $matches[ 0 ] as $key => $value ) {
			
			$dump = str_replace( $value, 'MongoId(\'' . $ids[ $key ] . '\')', $dump );
		
		}
		
		$dump = wordwrap( $dump, 230 );
		
		return $dump;
	
	}

	public static function outputForEdit( $data ) {
				
		$dump	= var_export( $data, true );
	
		$types	= array( 'MongoId', 'MongoCode', 'MongoDate', 'MongoRegex', 'MongoBinData', 'MongoInt32', 'MongoInt64', 'MongoDBRef', 'MongoMinKey', 'MongoMaxKey', 'MongoTimestamp' );
		
		foreach( $types as $type ) {
			
			$ids = self::getIds( $data, $type );
			$i		= -1;
			$dump	= preg_replace_callback( '/MongoId::__set_state\(array\(\s*\)\)/', function( $v ) use ( $ids, &$i, $type ) {
				
				$i++;
				
				return 'new ' . $type . '(\'' . $ids[ $i ] . '\')';
			
			}, $dump );
		
		}
		
		$i = 0;
				
		return $dump;
	
	}

    public static function getArrayKeys(array $array, $path = '', $drillDownDepthCount = 0) {
        $return = array();
        if ($drillDownDepthCount) {
            $path .= '.';
        }
        if (++$drillDownDepthCount < self::DRILL_DOWN_DEPTH_LIMIT) {
            foreach ($array as $key => $val) {
                $return[$id] = $id = $path . $key;
                if (is_array($val)) {
                    $return = array_merge($return, self::getArrayKeys($val, $id, $drillDownDepthCount));
                }
            }
        }
        return $return;
    }
	
}