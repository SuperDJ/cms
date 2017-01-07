<?php
class Plugins extends Database {
	private $_dir, // Plugins location
			$_directory = array(), // All installed plugins from plugins folder
			$_database = array(), // All plugins from database
			$_compareDir = array(), // All urls from directory
			$_compareDb = array(); // All urls from database

	function __construct() {
		parent::__construct();

		$this->_dir = PLUGINS;
		$this->_directory = $this->getFromDir($this->_dir); // Get all installed plugins from directory
		$this->_database = $this->getFromDb(); // Get all installed plugins from database

		// Compare arrays
		if( !$this->compare( $this->_directory, $this->_database ) ) {
			// If there are more plugins in database delete from database else insert plugins
			if( count( $this->_compareDb ) > count( $this->_compareDir ) ) {
				if( $this->delete( $this->_compareDir, $this->_compareDb ) ) {
					header('Location: ?path=plugins/overview&message=Plugins deleted&messageType=success');
				} else {
					header('Location: ?path=plugins/overview&message=Plugins not deleted&messageType=error');
				}
			} else {
				if( $this->insertInDb( $this->_directory ) ) {
					header('Location: ?path=plugins/overview&message=Plugins added&messageType=success');
				}
			}
		}
	}

	/**
	 * Get installed plugins from directory
	 *
	 * @param $dir
	 *
	 * @return array
	 */
	private function getFromDir( $dir ) {
		$cdir = scandir( $dir );
		$result = array();

		foreach( $cdir as $key => $value ) {
			if( !in_array( $value, array('.', '..') ) ) {
				if( is_dir( $dir . DIRECTORY_SEPARATOR . $value ) ) {
					$result[$value] = $this->getFromDir($dir . DIRECTORY_SEPARATOR . $value);
				} else {
					$result[] = $value;
				}
			}
		}

		return $result;
	}

	/**
	 * Insert plugins into database
	 *
	 * @param array  	$plugins 	All Plugins
	 * @param string	$parent		Parent plugin name
	 * @param string 	$parentID	Parent plugin id
	 */
	private function insertInDb( array $plugins, $parent = '', $parentID = '' ) {
		foreach( $plugins as $key => $value ) {
			if( is_array( $value ) ) {
				$url = $parent.$key.'/';

				if( !$this->exists('url', 'plugins', 'url', $url) ) {
					$plugin = ucfirst( $key );

					$stmt = $this->mysqli->prepare( "INSERT INTO `plugins` (`name`, `parent`, `url`) VALUES (?, ?, ?)" );
					$stmt->bind_param( 'sis', $plugin, $parentID, $url );
					$stmt->execute();

					$this->insertInDb($value, $url, $stmt->insert_id);

					$stmt->close();
				} else {
					$this->insertInDb($value, $url, $this->detail('id', 'plugins', 'url', $url));
				}
			} else {
				$url = $parent.substr( $value, 0, -4 );

				if( empty( $parentID ) ) {
					$parentID = $this->detail('id', 'plugins', 'url', $url);
				}

				if( !$this->exists('url', 'plugins', 'url', $url) ) {
					$plugin = ucfirst( substr( $value, 0, -4 ) );

					$stmt = $this->mysqli->prepare( "INSERT INTO `plugins` (`name`, `parent`, `url`) VALUES (?, ?, ?)" );
					$stmt->bind_param( 'sis', $plugin, $parentID, $url );
					$stmt->execute();
					$stmt->close();
				}
			}
		}
	}

	public function delete( array $dir, array $db ) {
		// Get difference
		$diff = array();

		foreach( $db as $key => $value ) {
			if( !in_array($value, $dir ) ) {
				 $diff[] = $value;
			}
		}

		// Delete difference
		$deleted = 0;
		foreach( $diff as $key => $value ) {
			$stmt = $this->mysqli->prepare("DELETE FROM `plugins` WHERE `url` = ?");
			$stmt->bind_param('s', $value);
			$stmt->execute();

			if( $stmt->affected_rows >= 1 ) {
				$deleted++;
			}
		}

		if( $deleted === count( $diff ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Get url for array comparing
	 *
	 * @param array  $plugins
	 * @param string $parent
	 * @param int    $i
	 * @param array  $url
	 *
	 * @return array|bool
	 */
	private function dirArray( array $plugins, $parent = '', $i = 0, $url = array() ) {
		foreach( $plugins as $key => $item ) {
			$i++;
			if( is_array( $item ) ) {
				$url[][] = $parent.$key.'/';
				$url[] = $this->dirArray( $item, $parent.$key.'/', $i );
			} else {
				$url[][] = $parent.substr( $item, 0, -4 );
			}
		}

		if( !empty( $url ) ) {
			return $url;
		} else {
			return false;
		}
	}

	/**
	 * Compare plugins from directory and database
	 *
	 * @param array $dirPlugins
	 * @param array $dbPlugins
	 *
	 * @return bool
	 */
	private function compare( array $dirPlugins, array $dbPlugins ) {
		$dirPlugins = array_flatten( $this->dirArray( $dirPlugins ) );
		$dbPlugins = array_flatten( $dbPlugins );

		// Remove numeric values from arrays
		$dir = array();
		foreach( $dirPlugins as $key => $value ) {
			if( !is_numeric( $value ) ) {
				$dir[] = $value;
			}
		}
		$this->_compareDir = $dir;

		$db = array();
		foreach( $dbPlugins as $key => $value ) {
			if( !is_numeric( $value ) ) {
				$db[] = $value;
			}
		}
		$this->_compareDb = $db;

		$countDir = count($dir);
		$countDb = count($db);

		if( $countDir !== $countDb) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Get urls from plugins form database
	 * @return array|bool
	 */
	private function getFromDb() {
		$stmt = $this->mysqli->query( "SELECT `url` FROM `plugins`" );

		$data = array();

		/*while( $row = $stmt->fetch_assoc() ) {
			$data[] = $row;
		}*/
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$data[] = $row;
		}

		if( !empty( $data ) ) {
			return $data;
		} else {
			return false;
		}
	}
}