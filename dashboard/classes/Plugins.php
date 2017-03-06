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
				if( $this->deleteFromDb( $this->_compareDir, $this->_compareDb ) ) {
					return true;
					//header('Location: ?path=plugins/overview&message=Plugins deleted&messageType=success');
				} else {
					return false;
					//header('Location: ?path=plugins/overview&message=Plugins not deleted&messageType=error');
				}
			} else {
				if( $this->insertInDb( $this->_directory ) ) {
					//header('Location: ?path=plugins/overview&message=Plugins added&messageType=success');
					return true;
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
	 * @param array  $plugins  All Plugins
	 * @param string $parent   Parent plugin name
	 * @param string $parentID Parent plugin id
	 *
	 * @return bool
	 */
	private function insertInDb( array $plugins, $parent = '', $parentID = '' ) {
		foreach( $plugins as $key => $value ) {
			if( is_array( $value ) ) {
				$url = $parent.$key.'/';

				if( !$this->exists('url', 'plugins', 'url', $url) ) {
					$plugin = ucfirst( str_replace( '-', ' ', $key ) );

					$stmt = $this->mysqli->prepare( "INSERT INTO `plugins` (`name`, `parent`, `url`) VALUES (:name, :parent, :url)" );
					$stmt->bindParam(':name', $plugin, PDO::PARAM_STR);
					$stmt->bindParam(':parent', $parentID, PDO::PARAM_INT);
					$stmt->bindParam(':url', $url, PDO::PARAM_STR);
					$stmt->execute();

					if( $stmt->rowCount() >= 1 ) {
					 	$stmt = null;
						$this->insertInDb( $value, $url, $stmt->lastInsertId() );
					} else {
						$stmt = null;
						return false;
					}
				} else {
					$this->insertInDb($value, $url, $this->detail('id', 'plugins', 'url', $url));
				}
			} else {
				$url = $parent.substr( $value, 0, -4 );

				if( empty( $parentID ) ) {
					$parentID = $this->detail('id', 'plugins', 'url', $url);
				}

				if( !$this->exists('url', 'plugins', 'url', $url) ) {
					$plugin = ucfirst( str_replace( '-', ' ', substr( $value, 0, -4 ) ) );

					$stmt = $this->mysqli->prepare( "INSERT INTO `plugins` (`name`, `parent`, `url`) VALUES (:name, :parent, :url)" );
					$stmt->bindParam(':name', $plugin, PDO::PARAM_STR);
					$stmt->bindParam(':parent', $parentID, PDO::PARAM_INT);
					$stmt->bindParam(':url', $url, PDO::PARAM_STR);
					$stmt->execute();

					if( $stmt->rowCount() >= 1 ) {
						$stmt = null;
					} else {
						$stmt = null;
						return false;
					}
				}
			}
		}
	}

	/**
	 * Delete plugins from database
	 *
	 * @param array $dir
	 * @param array $db
	 *
	 * @return bool
	 */
	private function deleteFromDb( array $dir, array $db ) {
		// Get difference
		$diff = array();

		foreach( $db as $key => $value ) {
			if( !in_array($value, $dir ) ) {
				 $diff[] = $value;
			}
		}

		//TODO remove preparing from foreach
		// Delete difference
		$deleted = 0;
		foreach( $diff as $key => $value ) {
			$stmt = $this->mysqli->prepare("DELETE FROM `plugins` WHERE `url` = ?");
			$stmt->execute( array( $value ) );

			if( $stmt->rowCount() >= 1 ) {
				$deleted++;
			}
		}

		$stmt = null;
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
		$stmt = $this->mysqli->prepare( "SELECT `url` FROM `plugins`" );
		$stmt->execute();

		$data = array();
		while( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
			$data[] = $row;
		}
		$stmt = null;

		if( !empty( $data ) ) {
			return $data;
		} else {
			return false;
		}
	}

	/**
	 * Get data from plugins
	 *
	 * @param int $id
	 *
	 * @return bool
	 */
	public function data( int $id = null ) {
		if( !is_null( $id ) ) {
			$stmt = $this->mysqli->prepare("SELECT `id`, `name`, `parent`, `icon`, `sort` FROM `plugins` WHERE `id` = :id LIMIT 1");
			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		} else {
			$stmt = $this->mysqli->prepare( "SELECT `id`, `name`, `parent`, `icon`, `sort` FROM `plugins`" );
		}
		$stmt->execute();

		if( $stmt->rowCount() >= 1 ) {
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt = null;
			return $result;
		} else {
			$stmt = null;
			return false;
		}
	}

	/**
	 * Edit plugin
	 *
	 * @param array $data
	 *
	 * @return bool
	 */
	public function edit( array $data ) {
		$icon = str_replace(' ', '_', $data['icon']);
		$stmt = $this->mysqli->prepare("UPDATE `plugins` SET `name` = :name, `icon` = :icon, `sort` = :sort WHERE `id` = :id");
		$stmt->bindParam(':name', $data['name'], PDO::PARAM_STR);
		$stmt->bindParam(':icon', $icon, PDO::PARAM_STR);
		$stmt->bindParam(':sort', $data['sort'], PDO::PARAM_INT);
		$stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
		$stmt->execute();

		if( $stmt->rowCount() >= 1 ) {
			$stmt = null;
			return true;
		} else {
			$stmt = null;
			return false;
		}
	}
}