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
		$mysqli = new PDO('mysql:host='.$this->_db->database->host.';dbname='.$this->_db->database->database.';charset=utf8', $this->_db->database->username, $this->_db->database->password);
		if( !$mysqli ) {
			print_r( $mysqli->errorInfo() );
		}

		if( TEST ) {
			$mysqli->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}

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
		$stmt = $this->mysqli->prepare("SELECT `$detail` FROM `$table` WHERE `$column` = :value");
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
	public function exists( $detail, $table, $column, $value ) {
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
		switch( gettype($value) ) {
			case 'integer':
				$value = (int)$value;
				break;
			case 'array':
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

	/**
	 * Insert items in database
	 *
	 * @param       $query
	 * @param array $columns
	 *
	 * @return bool
	 */
	public function insert( $query, array $columns ) {
		$stmt = $this->mysqli->prepare($query);
		$stmt->execute( array_values( $columns ) );

		if( $stmt->rowCount() >= 1 ) {
			$stmt = null;
			return true;
		} else {
			$stmt = null;
			return false;
		}
	}

	/**
	 * Delete items from database
	 *
	 * @param       $query
	 * @param array $columns
	 *
	 * @return bool
	 */
	/*public function delete( $query, array $columns ) {
		return $this->insert($query, $columns);
	}*/

	/**
	 * Update items in database
	 *
	 * @param       $query
	 * @param array $columns
	 *
	 * @return bool
	 */
	public function update( $query, array $columns ) {
		return $this->insert($query, $columns);
	}

	/**
	 * Select items from database
	 * @param       $query
	 * @param array $columns
	 * @param array $options
	 *
	 * @return array|bool
	 */
	public function select( $query, array $columns = array(), array $options = array() ) {
		$stmt = $this->mysqli->prepare($query);

		if( !empty( $columns ) ) {
			$stmt->execute( $columns );
		} else {
			$stmt->execute();
		}

		if( $stmt->rowCount() >= 1 ) {
			if( $stmt->rowCount() == 1 && in_array('multipleRows', $options ) ) {
				$result[] = $stmt->fetch( PDO::FETCH_ASSOC );
			} else {
				$result = $stmt->fetch( PDO::FETCH_ASSOC );
			}
			$stmt = null;
			return $result;
		} else {
			$stmt = null;
			return false;
		}
	}

	//TODO add possibility to execute (batches/ multidimensional array/ multiple rows at once)
	public function query( $query, array $columns = array(), array $options = array() ) {
		$stmt = $this->mysqli->prepare( $query );

		// Create execute
		if( !empty( $columns ) ) {
			//echo 1;
			// Check if named params are used
			if( in_array( 'named', $options ) ) {
				//echo 2;
				$count = count( $columns );
				$i = 0;
				foreach( $columns as $column => $value ) {
					$this->bind($stmt, $column, $value);
					$i++;
				}

				if( $count !== $i ) {
					echo 'Something went wrong binding params';
					return false;
				}
			} else {
				//echo 3;
				// If not false query is "SELECT" else query can be "INSERT", "UPDATE" or "DELETE"
				if( strpos( $query, 'SELECT' ) !== false ) {
					//echo 4;
					$stmt->execute( $columns );
				} else {
					//echo 5;
					$stmt->execute( array_values( $columns ) );
				}
			}
		} else {
			//echo 6;
			$stmt->execute();
		}

		// If not false query is "SELECT" else query can be "INSERT", "UPDATE" or "DELETE"
		if( strpos( $query, 'SELECT' ) !== false ) {
			//echo 7;
			if( $stmt->rowCount() >= 1 ) {
				//echo 8;
				if( $stmt->rowCount() == 1 && in_array('multipleRows', $options ) ) {
					//echo 9;
					$result[] = $stmt->fetch( PDO::FETCH_ASSOC );
				} else {
					//echo 10;
					$result = $stmt->fetchAll( PDO::FETCH_ASSOC );
				}
				$stmt = null;
				return $result;
			} else {
				//echo 11;
				$stmt = null;
				return false;
			}
		} else {
			if( $stmt->rowCount() >= 1 ) {
				//echo 12;
			 	$stmt = null;
			 	return true;
			} else {
				//echo 13;
				$stmt = null;
				return false;
			}
		}
	}

	private function bind( $stmt, $name, $value ) {
		$name = ':'.$name; // Add ":" to name

		switch( gettype( $value ) ) {
			case 'boolean':
				return $stmt->bindParam($name, $value, PDO::PARAM_BOOL);
				break;
			case 'integer':
				return $stmt->bindParam($name, $value, PDO::PARAM_INT);
				break;
			case 'double':
			case 'string':
				return $stmt->bindParam($name, $value, PDO::PARAM_STR);
			case 'null':
				return $stmt->bindParam($name, $value, PDO::PARAM_NULL);
		}
	}
}