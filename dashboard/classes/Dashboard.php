<?php
class Dashboard extends Database {
	public $path,
			$menu;

	private $_pluginsPath;

	function __construct( $path, $translate ) {
		parent::__construct();

		$this->path = $this->sanitize($path);

		$this->_pluginsPath = PLUGINS;

		$this->menu = $this->createMenu( $this->buildTree( $this->getMenuItems() ), $translate, $this->path );
	}

	/**
	 * Get all menu items
	 *
	 * @return array|bool
	 */
	private function getMenuItems() {
		$stmt = $this->mysqli->query("SELECT `id`, `name`, `parent`, `icon`, `url` FROM `plugins` WHERE `visible` = 1 ORDER BY `sort`");

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

	/**
	 * Get file from includes folder
	 *
	 * @param $type string	The type of file eg. footer, header
	 * @param $name	string  The name of the file eg. lr(Login register)
	 *
	 * @return bool|string
	 */
	public function getInclude( $type, $name = '' ) {
		if( empty( $type ) ) {
			return false;
		}

		if( !empty( $name ) ) {
			$file = 'includes/'.$name.'-'.$type.'.php';
		} else {
			$file = 'includes/'.$type.'.php';
		}

		if( file_exists( $file ) ) {
			return $file;
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
	public function checkPath( $path ) {
		if( file_exists( $this->_pluginsPath.'/'.$path.'.php') ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Build menu tree
	 *
	 * @param array $elements
	 * @param int   $parentId
	 *
	 * @return array
	 */
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

	/**
	 * Generate menu items
	 *
	 * @param array    $plugins
	 * @param callable $translate
	 * @param          $url
	 *
	 * @return bool|string
	 */
	private function createMenu( array $plugins, callable $translate, $url ) {
		$html = '';

		foreach( $plugins as $fields => $field ) {
			if( !empty( $field['children'] ) ) {
				$html .= '	<li class="sc-drawer-dropdown">
						    	'.( !empty( $field['icon'] ) ? '<i class="material-icons">'.$field['icon'].'</i>' : '' ).'
								'.$translate( $field['name'] ).'
								<i class="material-icons sc-arrow">expand_more</i>
								
								<ul>';
				$html .= $this->createMenu( $field['children'], $translate, $url );
				$html .= '		</ul>';
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