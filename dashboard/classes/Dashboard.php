<?php
//TODO Load all CMS settings from database
class Dashboard {
	public $path,
			$menu;

	private $_pluginsPath,
			$_db;

	function __construct( Database $db = null, $path, $translate ) {
		if( !is_null( $db ) ) {
			$this->_db = $db;
		}

		$this->path = $this->_db->sanitize($path);

		$this->_pluginsPath = PLUGINS;

		if( !empty( $_SESSION['user']['id'] ) ) {
			$this->menu = $this->createMenu( $this->buildTree( $this->getMenuItems() ), $translate, $this->path );
		}
	}

	/**
	 * Get all menu items
	 *
	 * @return array|bool
	 */
	private function getMenuItems() {
		// TODO add check for user permission
		$stmt = $this->_db->mysqli->prepare("
SELECT `p`.`id`, `name`, `parent`, `icon`, `url` FROM `plugins` `p`
JOIN `rights` `r`
  ON `p`.`id` = `r`.`plugins_id`
WHERE `visible` = 1 AND `r`.`groups_id` = :groups_id ORDER BY `sort`");
		$stmt->bindParam(':groups_id', $_SESSION['user']['group'], PDO::PARAM_INT);
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
				$html .= '	<a class="sc-drawer-dropdown">
						    	'.( !empty( $field['icon'] ) ? '<i class="material-icons">'.$field['icon'].'</i>' : '' ).'
								'.$translate( $field['name'] ).'
								<i class="material-icons sc-arrow">expand_more</i>
							</a>	
								
								<div class="sc-dropdown">';
				$html .= $this->createMenu( $field['children'], $translate, $url );
				$html .= '		</div>';
			} else {
				$html .= '	<a href="?path='.$field['url'].'" '.( $url == $field['url'] ? 'class="sc-active"' : '' ).'>
								'.( !empty( $field['icon'] ) ? '<i class="material-icons">'.$field['icon'].'</i>' : '' ).'
								'.$translate( $field['name'] ).'
							</a>';
			}
		}

		if( !empty( $html ) ) {
			return $html;
		} else {
			return false;
		}
	}

	/**
	 * Get plugin name
	 *
	 * @return mixed
	 */
	public function plugin() {
		$plugin = explode( '/', $this->path );
		$count = count($plugin);

		if( $count > 2 ) {
			return $plugin[ $count - 1 ];
		} else {
			return $plugin[0];
		}
	}
}