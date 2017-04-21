<?php
/**
 * Class Database
 * Handles database connection and database requests
 */
class Database {
	public $mysqli = false;

	// Set database credentials
	private $_db = array();

	function __construct() {
		// Credentials
		if( file_exists( ROOT.'credentials.json' ) ) {
			$this->_db = json_decode( file_get_contents( ROOT.'credentials.json' ) );

			// Get connection
			if( !$this->mysqli ) {
				$this->mysqli = $this->connect();
			}
		} else {
			echo 'Could not connect to db';
		}
	}

	/**
	 * Get database connection
	 * 
	 * @return bool|\PDO
	 */
	private function connect() {
		$mysqli = new PDO('mysql:host='.$this->_db->database->host.';dbname='.$this->_db->database->database.';charset=utf8', $this->_db->database->username, $this->_db->database->password);

		// Only execute when in test mode
		if( TEST ) {
			$mysqli->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$mysqli->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		}

		if( $mysqli ) {
			return $mysqli;
		} else {
			return false;
		}
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
	public function detail( string $detail, string $table, string $column, $value ) {
		$stmt = $this->mysqli->prepare("SELECT `$detail` FROM `$table` WHERE `$column` = :value LIMIT 1");
		$stmt->bindParam(':value', $value, ( is_numeric( $value ) ? PDO::PARAM_INT : PDO::PARAM_STR ));
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC)[$detail];

		if( $stmt->rowCount() >= 1 ) {
			$stmt = null;
			return $result;
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
	public function exists( string $detail, string $table, string $column, $value ) {
		$stmt = $this->mysqli->prepare("SELECT `$detail` FROM `$table` WHERE `$column` = :value LIMIT 1");
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
	 * Count existence
	 *
	 * @param string $detail Column name
	 * @param string $table  Table name
	 * @param string $column Column name
	 * @param string|int $value  Value
	 *
	 * @return bool Return true or false depending if value already exists in database
	 */
	public function count( string $detail, string $table, string $column, $value ) {
		$stmt = $this->mysqli->prepare("SELECT `$detail` FROM `$table` WHERE `$column` = :value");
		$stmt->bindParam(':value', $value, ( is_numeric( $value ) ? PDO::PARAM_INT : PDO::PARAM_STR ));
		$stmt->execute();
		$count = $stmt->rowCount();
		$stmt = null;

		if( $count >= 1 ) {
			return $count;
		} else {
			return false;
		}
	}

	/**
	 * Sanitize input of malicious characters (protect against sql-injection)
	 *
	 * @param string|int $value
	 * @param bool       $html
	 *
	 * @return string
	 */
	public function sanitize( string $value, bool $html = false ) {
		if( gettype( $value ) == 'integer' ) {
			$value = (int)$value;
		}

		if( $html === true ) {
			return trim( $value );
		} else {
			if( gettype( $value ) == 'array' ) {
				return $value;
			} else {
				return trim( htmlentities( strip_tags( stripslashes( $value ) ) ) );
			}
		}
	}
}
