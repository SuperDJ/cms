<?php
class Session {
	/**
	 * Set a session
	 *
	 * @param string $name  Session name
	 * @param string|array $value Session value
	 *
	 * @return bool
	 */
	public function set( $name, $value ) {
		if( is_array( $value ) || is_object( $value ) ) {
			$_SESSION[$name] = $value;
			return true;
		} else {
			$_SESSION[$name] = base64_encode( $value );
			return true;
		}
	}

	/**
	 * Get session value
	 *
	 * @param  string $name Session name
	 *
	 * @return bool|string|int  Session value
	 */
	public function get( $name ) {
		if( $this->exists( $name ) ) {
			if( is_array( $_SESSION[$name] ) || is_object( $_SESSION[$name] ) ) {
				return $_SESSION[$name];
			} else {
				return base64_decode( $_SESSION[$name] );
			}
		} else {
			return false;
		}
	}

	/**
	 * Check if a session exists
	 *
	 * @param  string $name Session name
	 *
	 * @return bool type       Return true if session exits
	 */
	public function exists( $name ) {
		if( !empty( $_SESSION[$name] ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Delete session
	 *
	 * @param  string $name Session name
	 *
	 * @return bool       Return true if the session has been deleted
	 */
	public function delete( $name ) {
		if( $this->exists( $name ) ) {
			// Unset all sessions
			unset( $_SESSION[$name] );

			// Check again if the session exists
			if( $this->exists( $name ) ) {
				return false;
			} else {
				return true;
			}
		} else {
			return false;
		}
	}
}