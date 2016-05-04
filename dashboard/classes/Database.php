<?php
class Database {
	public $mysqli;

	// Set database credentials
	private $_db = array(
		'username' => 'derkjkn43_cms',
		'password' => 'ZDRKsbYSNX',
		'host' => '10.3.0.103',
		'database' => 'derkjkn43_cms'
	);

	function __construct() {
		$this->mysqli = new mysqli($this->_db['host'], $this->_db['username'], $this->_db['password'], $this->_db['database']);

		if( !$this->mysqli ) {
			echo $this->mysqli->error;
		}

		// Set database character set
		if( !$this->mysqli->set_charset('utf8') ) {
			echo $this->mysqli->error;
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
	public function detail( $detail, $table, $column, $value ) {
		$stmt = $this->mysqli->prepare("SELECT `$detail` FROM `$table` WHERE `$column` = ?");

		if( is_numeric( $value ) ) {
		   $stmt->bind_param('i', $value);
		} else {
			$stmt->bind_param('s', $value);
		}

		$stmt->execute();
		$stmt->bind_result($detail);
		$stmt->fetch();

		$stmt->close();
		return $detail;
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
		$stmt = $this->mysqli->prepare("SELECT `$detail` FROM `$table` WHERE `$column` = ?");

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
			return $this->mysqli->real_escape_string( trim( $value ) );
		} else {
			return $this->mysqli->real_escape_string( trim( htmlentities( strip_tags( stripslashes( $value ) ) ) ) );
		}
	}
}