<?php
class Cookie {

	/**
	 * [Set cookie]
	 *
	 * @param string                $name  [The name of the cookie]
	 * @param string|bool|int|array $value [The value of the cookie]
	 * @param string                $time  [The time period for the cookie]
	 *
	 * @return bool
	 */
    public function set( $name, $value, $time ) {
        if( is_array( $value ) ) {
            setcookie( $name, base64_encode( serialize( $value ) ), time()+ $time, '/' );
			return true;
        } else {
            setcookie( $name, base64_encode( $value ), time()+ $time, '/' );
			return true;
        }
    }

	/**
     * [Get cookie]
     * @param  string $name [The name of the cookie]
     * @return string|bool|int|array       [The value of the cookie]
     */
    public function get( $name ) {
        if( $this->exists( $name ) ) {
			$cookie = base64_decode( $_COOKIE[$name] );

			if( is_serialized( $cookie ) ) {
				return unserialize( $cookie );
			} else {
				return $cookie;
			}
        } else {
            return false;
        }
    }

	/**
     * [Cookie exists]
     * @param  string $name [The name of the cookie]
     * @return bool       [Return true or false depending if the cookie exists]
     */
    public function exists( $name ) {
        if( !empty( $_COOKIE[$name] ) ) {
            return true;
        } else {
            return false;
        }
    }

	/**
	 * [Delete cookie]
	 * @param  string $name [The name of the cookie]
	 * @param  int $time [The time the cookie was set for]
	 * @return bool       [Return true or false depending if the cookie was deleted]
	 */
    public function delete( $name, $time ) {
        if( $this->exists( $name ) ) {
            unset( $_COOKIE[$name] );
            setcookie( $name, '', time()- $time, '/' );

            // Check if the the cookie is really deleted
            if( $this->exists( $name ) === false ) {
            	return true;
            } else {
            	return false;
            }
        } else {
            return false;
        }
    }
}