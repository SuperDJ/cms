<?php
class Plugins extends Database {
	private $_dir, // Plugins location
			$_dirPlugins = array(), // All installed plugins from plugins folder
			$_dbPlugins = array(); // All plugins from database

	public 	$path,  // Current path
			$dirPlugins,
			$dbPlugins,
			$menu = array(); // Create menu

	function __construct( $path ) {
		parent::__construct();

		$this->_dir = ROOT.'plugins';
		$this->_dirPlugins = $this->get($this->_dir); // Get all installed plugins
		$this->path = $path; // Save path

		// TODO Check if all plugins are still installed else delete
		$this->insert( $this->_dirPlugins );
		$this->_dbPlugins = $this->dbPlugins();
		$this->menu = $this->createMenu($this->_dbPlugins);

		$this->dirPlugins = $this->_dirPlugins;
		$this->dbPlugins = $this->_dbPlugins;
	}

	/**
	 * Get installed plugins
	 *
	 * @param $dir
	 *
	 * @return array
	 */
	private function get( $dir ) {
		$cdir = scandir( $dir );
		$result = array();

		foreach( $cdir as $key => $value ) {
			if( !in_array( $value, array('.', '..') ) ) {
				if( is_dir( $dir . DIRECTORY_SEPARATOR . $value ) ) {
					$result[$value] = $this->get($dir . DIRECTORY_SEPARATOR . $value);
				} else {
					$result[] = $value;
				}
			}
		}

		return $result;
	}

	private function insert( array $plugins, $parent = '', $parentID = '' ) {
		if( empty( $parentID ) ) {
			$parentID = 0;
		}

		foreach( $plugins as $key => $value ) {
			if( is_array( $value ) ) {
				$url = $parent.$key.'/';

				if( !$this->exists('url', 'plugins', 'url', $url) ) {
					$plugin = ucfirst( $key );

					$stmt = $this->mysqli->prepare( "INSERT INTO `plugins` (`plugin`, `parent`, `url`) VALUES (?, ?, ?)" );
					$stmt->bind_param( 'sis', $plugin, $parentID, $url );
					$stmt->execute();

					$this->insert($value, $url, $stmt->insert_id);

					$stmt->close();
				} else {
					$this->insert($value, $url, $this->detail('id', 'plugins', 'url', $url));
				}
			} else {
				$url = $parent.substr( $value, 0, -4 );

				if( !$this->exists('url', 'plugins', 'url', $url) ) {
					$plugin = ucfirst( substr( $value, 0, -4 ) );

					$stmt = $this->mysqli->prepare( "INSERT INTO `plugins` (`plugin`, `parent`, `url`) VALUES (?, ?, ?)" );
					$stmt->bind_param( 'sis', $plugin, $parentID, $url );
					$stmt->execute();
					$stmt->close();
				}
			}
		}
	}

	private function dbPlugins() {
		$stmt = $this->mysqli->prepare( "SELECT `id`, `name`, `parent`, `icon`, `url`, `visible`, `sort` FROM `plugins`" );
		$stmt->execute();
		$result = $stmt->get_result();

		$data = array();
		while( $row = $result->fetch_assoc() ) {
			$data[$row['id']] = $row;
		}
		$stmt->close();

		if( !empty( $data ) ) {
			return $data;
		} else {
			return false;
		}
	}

	/**
	 * Get breadcrumb
	 *
	 */
	public function breadcrumbs() {

	}

	/**
	 * Get type of path eg. edit, delete, add, overview
	 *
	 * @return mixed
	 */
	public function pathType() {
		return end( explode ( '/', $this->path ) );
	}

	/**
	 * Check if path exists
	 *
	 * @param $path
	 *
	 * @return bool
	 */
	public function check( $path ) {
		if( file_exists( $this->_dir.'/'.$path.'.php') ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Get required header
	 *
	 * @param string $name
	 *
	 * @return string
	 */
	public function getHeader( $name = '' ) {
		if( !empty( $name ) ) {
			if( file_exists( ROOT.'includes/'.$name.'-header.php' ) ) {
				return ROOT.'includes/'.$name.'-header.php';
			}
		} else {
			return ROOT.'includes/header.php';
		}
	}

	/**
	 * Get required footer
	 *
	 * @param string $name
	 *
	 * @return string
	 */
	public function getFooter( $name = '' ) {
		if( !empty( $name ) ) {
			if( file_exists( ROOT.'includes/'.$name.'-footer.php' ) ) {
				return ROOT.'includes/'.$name.'-footer.php';
			}
		} else {
			return ROOT.'includes/footer.php';
		}
	}

	public function createMenu( $plugins ) {
		return $plugins;
	}
}