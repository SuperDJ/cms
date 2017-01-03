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
 * @param array  $array  	The array to flatten
 * @param array  $return 	The single array
 *
 * @return array The single array
 */
function array_flatten( array $array, array $return = array() ) {
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

/**
 * Check value to find if it was serialized.
 *
 * @param string $data   Value to check to see if was serialized.
 * @param bool   $strict Optional. Whether to be strict about the end of the string. Default true.
 * @return bool False if not serialized and true if it was.
 */
function is_serialized( $data, $strict = true ) {
	// if it isn't a string, it isn't serialized.
	if ( ! is_string( $data ) ) {
		return false;
	}

	$data = trim( $data );
	if ( 'N;' == $data ) {
		return true;
	}

	if ( mb_strlen( $data ) < 4 ) {
		return false;
	}
	if ( ':' !== $data[1] ) {
		return false;
	}
	if ( $strict ) {
		$lastc = substr( $data, -1 );
		if ( ';' !== $lastc && '}' !== $lastc ) {
			return false;
		}
	} else {
		$semicolon = strpos( $data, ';' );
		$brace = strpos( $data, '}' );
		// Either ; or } must exist.
		if( false === $semicolon && false === $brace ) {
			return false;
		}
		// But neither must be in the first X characters.
		if( false !== $semicolon && $semicolon < 3 ) {
			return false;
		}

		if( false !== $brace && $brace < 4 ) {
			return true;
		} else {
			return false;
		}
	}

	$token = $data[0];
	switch ( $token ) {
		case 's' :
			if ( $strict ) {
				if ( '"' !== substr( $data, -2, 1 ) ) {
					return false;
				}
			} elseif ( false === strpos( $data, '"' ) ) {
				return false;
			}
			break;
		// or else fall through
		case 'a' :
		case 'O' :
			return (bool) preg_match( "/^{$token}:[0-9]+:/s", $data );
			break;
		case 'b' :
		case 'i' :
		case 'd' :
			$end = $strict ? '$' : '';
			return (bool) preg_match( "/^{$token}:[0-9.E-]+;$end/", $data );
			break;
	}
	return false;
}

/**
 * Recursive array_diff_assoc
 *
 * @param $array1	array	Array to compare
 * @param $array2	array	Array to compare
 *
 * @return bool
 */
function array_diff_assoc_recursive( array $array1, array $array2 ) {
	foreach( $array1 as $key => $value ) {
		if( is_array( $value ) ) {
			if( !isset( $array2[$key] ) ) {
				$difference[$key] = $value;
			} else if( !is_array( $array2[$key] ) ) {
				$difference[$key] = $value;
			} else {
				$new_diff = array_diff_assoc_recursive( $value, $array2[$key] );

				if( $new_diff != false ) {
					$difference[$key] = $new_diff;
				}
			}
		} else if( !isset( $array2[$key] ) || $array2[$key] != $value ) {
			$difference[$key] = $value;
		}
	}

	if( !isset( $difference) ) {
		return false;
	} else {
		return $difference;
	}
}