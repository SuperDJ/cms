<?php
class Plugins extends Database {
	private $_dir, // Plugins location
			$_plugins = array(); // All installed plugins

	public 	$plugins = array(), // All plugins from database
			$path;

	function __construct( $path ) {
		parent::__construct();

		$this->_dir = ROOT.'/dashboard/plugins';
		$this->_plugins = $this->get($this->_dir); // Get all installed plugins
		$this->path = $path; // Save path
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
			if( file_exists( ROOT.'/dashboard/includes/'.$name.'-header.php' ) ) {
				return ROOT.'/dashboard/includes/'.$name.'-header.php';
			}
		} else {
			return ROOT.'/dashboard/includes/header.php';
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
			if( file_exists( ROOT.'/dashboard/includes/'.$name.'-footer.php' ) ) {
				return ROOT.'/dashboard/includes/'.$name.'-footer.php';
			}
		} else {
			return ROOT.'/dashboard/includes/footer.php';
		}
	}
}