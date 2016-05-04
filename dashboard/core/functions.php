<?php
/**
 * Get IP address
 *
 * @return bool
 */
function getClientIP() {
	if( isset( $_SERVER ) ) {
		if( isset($_SERVER["HTTP_X_FORWARDED_FOR"]) ) {
			return $_SERVER["HTTP_X_FORWARDED_FOR"];
		} else if( isset( $_SERVER["HTTP_CLIENT_IP"] ) ) {
			return $_SERVER["HTTP_CLIENT_IP"];
		} else {
			return $_SERVER["REMOTE_ADDR"];
		}
	} else {
		return false;
	}
}

/**
 * Array diff multidimensional
 *
 * @param array $array1 Array 1
 * @param array $array2 Array 2
 *
 * @return array Array with differences
 */
function array_diff_multi( array $array1, array $array2 ) {
	if( !is_array( $array1 ) && !is_array( $array2 ) ) {
		return false;
	}

	$result = array();
	foreach( $array1 as $key => $val ) {
		if( isset( $array2[$key] ) ) {
			if( is_array( $val ) && $array2[$key] ) {
				$result[$key] = array_diff_multi( $val, $array2[$key] );
			}
		} else {
			$result[$key] = $val;
		}
	}

	return $result;
}

/**
 * List array
 *
 * @param array $array
 *
 * @return string        Return html list of array
 */
function listArray( array $array ) {
	if( !is_array( $array ) ) {
		return false;
	}

	$html = '';
	foreach ( $array as $key => $item ) {
		if ( is_array( $item ) ) {
			$html .= '<li>'.$key.'<ul>';
			$html .= listArray($item);
			$html .= '</ul></li>';
		} else {
			$html .= '<li>'.$item.'</li>';
		}
	}
	return $html;
}

/**
 * From multidimensional array to single array
 *
 * @param array $array  The array to flatten
 * @param array  $return The single array
 *
 * @return array The single array
 */
function array_flatten( array $array, $return = array() ) {
	if( !is_array( $array ) ) {
		return false;
	}

	foreach( $array as $key => $value ) {
		if( is_array( $value ) ) {
			$return[] = $key;
			$return = array_flatten( $value, $return );
		} else {
			if( $value ) {
				$return[] = $value;
			}
		}
	}

	return $return;
}

/**
 * Get keys of a multidimensional array
 * @param  array  $array Array to get the keys of
 * @return array        All keys
 */
function array_keys_multi( array $array ) {
	if( !is_array( $array ) ) {
		return false;
	}

	$keys = array();
	foreach( $array as $key => $value ) {
		$keys[] = $key;

		if( is_array( $array[$key] ) ) {
			$keys = array_merge( $keys, array_keys_multi($array[$key]) );
		}
	}

	return $keys;
}

/**
 * Count multidimensional array
 *
 * @param array $array     Array to count
 * @param int   $count_key Set to 1 to count keys else don't
 *
 * @return int The total number of items in array
 */
function count_multi( array $array, $count_key = 1 ) {
	if( !is_array( $array ) ) {
		return false;
	}

	$count = 0;
	foreach ( $array as $type ) {
		if( is_array( $type ) ) {
			$count += count_multi($type);

			if( $count_key ) {
				$count++; // Adding +1 for key
			}
		} else {
			$count += count( $type );
		}
	}

	return $count;
}

/**
 * Array search for multidimensional array
 *
 * @param int|string|bool $value The value to search
 * @param array  $array The array to search
 *
 * @return string|int The key with the value
 */
function array_search_multi( $value, array $array ) {
	if( !is_array( $array ) ) {
		return false;
	}

	$table = '';
	foreach( $array as $key => $values ) {
		if( in_array( $value, $values ) ) {
			$table = $key;
		}
	}
	return $table;
}