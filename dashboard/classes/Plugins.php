<?php
class Plugins extends Database {
	private $_dir, // Plugins location
			$_directory = array(), // All installed plugins from plugins folder
			$_database = array(); // All plugins from database

	public 	$path,  // Current path
			$directory,
			$database,
			$menu; // Store menu

	function __construct( $path ) {
		parent::__construct();

		$this->_dir = ROOT.'plugins';
		$this->_directory = $this->get($this->_dir); // Get all installed plugins
		$this->path = $path; // Save path

		// TODO Check if all plugins are still installed else delete
		$this->insert( $this->_directory );
		$this->_database = $this->database();
		$this->menu = $this->buildTree($this->_database);

		$this->directory = $this->_directory;
		$this->database = $this->_database;
	}

	/**
	 * Get installed plugins from directory
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

	/**
	 * Insert plugins into database
	 *
	 * @param array  	$plugins 	All Plugins
	 * @param string	$parent		Parent plugin name
	 * @param string 	$parentID	Parent plugin id
	 */
	private function insert( array $plugins, $parent = '', $parentID = '' ) {
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

				if( empty( $parentID ) ) {
					$parentID = $this->detail('id', 'plugins', 'url', $url);
				}

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

	private function database() {
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

	private function buildTree( array $elements, $parentId = 0 ) {
		$branch = array();

		foreach( $elements as $element ) {
			if( $element['parent'] == $parentId ) {
				$children = $this->buildTree( $elements, $element['id'] );

				if( $children ) {
					$element['children'] = $children;
				}

				$branch[] = $element;
			}
		}

		return $branch;
	}

	public function createMenu( array $plugins, callable $translate, $url ) {
		$html = '';

		foreach( $plugins as $fields => $field ) {
			if( !empty( $field['children'] ) ) {
				$html .= '<li class="sc-drawer-dropdown">'.$translate( $field['name'] ).'<ul>';
				$html .= $this->createMenu( $field['children'], $translate, $url );
				$html .= '</ul>';
			} else {
				$html .= '	<li>
								<a href="?path='.$field['url'].'" '.( $url == $field['url'] ? 'class="sc-active"' : '' ).'>
									'.( !empty( $field['icon'] ) ? '<i class="material-icons">'.$field['icon'].'</i>' : '' ).'
									'.$translate( $field['name'] ).'
								</a>
							</li>';
			}
		}

		if( !empty( $html ) ) {
			return $html;
		} else {
			return false;
		}
	}
}