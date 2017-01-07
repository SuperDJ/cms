<?php
/**
 * Class Database
 * Handles database connection and database requests
 */
class Database {
	public $mysqli;

	// Set database credentials
	private $_db = array();

	function __construct() {
		// Credentials
		if( file_exists( ROOT.'credentials.json' ) ) {
			$this->_db = json_decode( file_get_contents( ROOT.'credentials.json' ) );

			// Get connection
			$this->connect();
		} else {
			echo 'Could not connect to db';
		}
	}

	/**
	 * Create database connection
	 */
	private function connect() {
		//$mysqli = new mysqli( $this->_db->database->host, $this->_db->database->username, $this->_db->database->password, $this->_db->database->database );
		$mysqli = new PDO('mysql:host='.$this->_db->database->host.';dbname='.$this->_db->database->database.';charset=utf8', $this->_db->database->username, $this->_db->database->password);
		if( !$mysqli ) {
			echo $mysqli->error;
		}

		// Set database character set
		/*if( !$mysqli->set_charset( 'utf8' ) ) {
			echo $mysqli->error;
		}*/

		$this->mysqli = $mysqli;
	}

	/**
	 * Get a single value from the database
	 *
	 * @param string $detail Column name
	 * @param string $table  Table name
	 * @param string $column Column name
	 * @param string|int $value  Value
	 *
	 * @return string|bool Return string or bool depending on value in database
	 */
	public function detail( $detail, $table, $column, $value ) {
		/*$stmt = $this->mysqli->prepare("SELECT `$detail` FROM `$table` WHERE `$column` = ?");

		if( is_numeric( $value ) ) {
		   $stmt->bind_param('i', $value);
		} else {
			$stmt->bind_param('s', $value);
		}

		$stmt->execute();
		$stmt->bind_result($detail);
		$stmt->fetch();

		$stmt->close();
		return $detail;*/

		$stmt = $this->mysqli->prepare("SELECT `$detail` FROM `$table` WHERE `$column` = :value");
		$stmt->bindParam(':value', $value, ( is_numeric( $value ) ? PDO::PARAM_INT : PDO::PARAM_STR ));
		$stmt->execute();

		if( $stmt->rowCount() >= 1 ) {
			$stmt = null;
			return $stmt->fetch(PDO::FETCH_ASSOC);
		} else {
			$stmt = null;
			return false;
		}
	}

	/**
	 * Check if exists
	 *
	 * @param string $detail Column name
	 * @param string $table  Table name
	 * @param string $column Column name
	 * @param string|int $value  Value
	 *
	 * @return bool Return true or false depending if value already exists in database
	 */
	public function exists( $detail, $table, $column, $value ) {
		/*$stmt = $this->mysqli->prepare("SELECT `$detail` FROM `$table` WHERE `$column` = ?");

		if( is_numeric( $value ) ) {
			$stmt->bind_param('i', $value);
		} else {
			$stmt->bind_param('s', $value);
		}

		$stmt->execute();
		$stmt->store_result(); // Add this to prevent return of 0

		if( $stmt->num_rows >= 1 ) {
			$stmt->close();
			return true;
		} else {
			$stmt->close();
			return false;
		}*/

		$stmt = $this->mysqli->prepare("SELECT `$detail` FROM `$table` WHERE `$column` = :value");
		$stmt->bindParam(':value', $value, ( is_numeric( $value ) ? PDO::PARAM_INT : PDO::PARAM_STR ));
		$stmt->execute();

		if( $stmt->rowCount() >= 1 ) {
			$stmt = null;
		 	return true;
		} else {
			$stmt = null;
			return false;
		}
	}

	/**
	 * Sanitize input of malicious characters (protect against sql-injection)
	 *
	 * @param string|int $value [description]
	 * @param bool       $html  [description]
	 *
	 * @return string [type] [description]
	 */
	public function sanitize( $value, $html = false ) {
		// Check if $value is numeric or an array
		// If so change the way of sanitizing
		switch( $value ) {
			case is_numeric( $value ):
				$value = (int)$value;
				break;
			case is_array( $value ):
				$value = json_encode( $value );
				break;
		}

		if( $html === true ) {
			//return $this->mysqli->real_escape_string( trim( $value ) );
			return trim( $value );
		} else {
			//return $this->mysqli->real_escape_string( trim( htmlentities( strip_tags( stripslashes( $value ) ) ) ) );
			return trim( htmlentities( strip_tags( stripslashes( $value ) ) ) );
		}
	}
}