<?php
class Group extends Database implements Plugin {
	/**
	 * Add group to database
	 *
	 * @param array $data
	 *
	 * @return bool
	 */
	public function add( array $data ) {
		$q = 0; // Store completed queries
		$stmt = $this->mysqli->prepare("INSERT INTO `groups` (`group`, `description`, `default`) VALUES (:group, :description, :default)");
		$stmt->bindParam(':group', $data['group'], PDO::PARAM_STR);
		$stmt->bindParam(':description', $data['description'], PDO::PARAM_STR);
		$stmt->bindParam(':default', $data['default'], PDO::PARAM_INT);
		$stmt->execute();

		$id = $this->mysqli->lastInsertId(); // Store insert id
		if( $stmt->rowCount() >= 1 ) {
			$stmt = null;
			$q++;
		} else {
			$stmt = null;
			return false;
		}

		// Add rights
		if( $this->addRights($data, $id) ) {
			$q++;
		}


		// If both queries executed
		if( $q === 2 ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Add rights to group
	 *
	 * @param array $data
	 * @param int   $id
	 *
	 * @return bool
	 */
	private function addRights( array $data, int $id ) {
		// Create query to insert all plugins for group
		$query = "INSERT INTO `rights` (`groups_id`, `plugins_id`) VALUES ";
		$insertQuery = array();
		$insertData = array();

		// $value is always 1
		foreach( $data as $plugin_id => $value ) {
			echo $id.' '.$plugin_id;
			if( is_numeric( $plugin_id ) ) {
				$insertQuery[] = '(?, ?)';
				$insertData[] = $id;
				$insertData[] = $plugin_id;
			}
		}

		if( !empty( $insertQuery ) ) {
			$query .= implode( ', ', $insertQuery );
			$stmt = $this->mysqli->prepare($query);
			$stmt->execute($insertData);

			if( $stmt->rowCount() >= 1 ) {
				$stmt = null;
				return true;
			} else {
				$stmt = null;
				return false;
			}
		}
	}

	/**
	 * Delete group from database
	 *
	 * @param int $id
	 *
	 * @return bool
	 */
	public function delete( int $id ) {
		$stmt = $this->mysqli->prepare("DELETE FROM `groups` WHERE `id` = :id");
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
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
	 * Delete rights
	 *
	 * @param array $delete
	 * @param array $data
	 *
	 * @return bool
	 */
	private function deleteRights( array $delete, array $data ) {
		$stmt = $this->mysqli->prepare("DELETE FROM `rights` WHERE `groups_id` = :groups_id AND `plugins_id` = :plugins_id");

		$count = count( $delete );
		$i = 0;
		foreach( $delete as $key => $row ) {
			$stmt->bindParam(':groups_id', $data['id'], PDO::PARAM_INT);
			$stmt->bindParam(':plugins_id', $row, PDO::PARAM_INT);
			$stmt->execute();

			if( $stmt->rowCount() >= 1 ) {
				$i++;
			}
		}

		$stmt = null;
		if( $count === $i ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Get data from database
	 *
	 * @param int|null $id
	 *
	 * @return bool
	 */
	public function data( int $id = null ) {
		$query = "
			SELECT `g`.`id`, `group`, `default`, `description`, concat( round( ( COUNT(`r`.`id`) / `plugins` ) * 100 ), '%' ) as `rights`
			FROM `groups` `g`
			JOIN (
				SELECT COUNT(`id`) `plugins`
				FROM `plugins`
				) `p`
			JOIN `rights` `r`
			  	ON `r`.`groups_id` = `g`.`id`
		";

		if( !is_null( $id ) ) {
			$query .= "
				WHERE `g`.`id` = :id
				GROUP BY `g`.`id`
				LIMIT 1
			";
			$stmt = $this->mysqli->prepare($query);
			$stmt->bindParam( ':id', $id, PDO::PARAM_INT );
		} else {
			$query .= "GROUP BY `g`.`id`";
			$stmt = $this->mysqli->prepare($query);
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
	 * Get all rights from specific group
	 *
	 * @param int $id
	 *
	 * @return bool
	 */
	public function rights( int $id ) {
		$stmt = $this->mysqli->prepare("SELECT `plugins_id` FROM `rights` WHERE `groups_id` = :id");
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
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
	 * Edit a group
	 *
	 * @param array $data
	 *
	 * @return bool
	 */
	public function edit( array $data ) {
		$q = 0; // Store query success
		// Check if description or group needs to be update
		if( $data['description'] != $this->detail('description', 'groups', 'id', $data['id']) &&
			$data['group'] != $this->detail('group', 'groups', 'id', $data['id']) &&
			$data['default'] != $this->detail('default', 'groups', 'id', $data['id'])
		) {
			if( !empty( $data['default'] ) ) {
				$stmt = $this->mysqli->prepare("UPDATE `groups` SET `group` = :group, `description` = :description, `default` = :default WHERE `id` = :id");
				$stmt->bindParam(':default', $data['default'], PDO::PARAM_INT);
			} else {
				$stmt = $this->mysqli->prepare("UPDATE `groups` SET `group` = :group, `description` = :description, `default` = DEFAULT WHERE `id` = :id");
			}
			$stmt->bindParam(':group', $data['group'], PDO::PARAM_STR);
			$stmt->bindParam(':description', $data['description'], PDO::PARAM_STR);
			$stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
			$stmt->execute();

			if( $stmt->rowCount() >= 1 ) {
				$stmt = null;
				$q++;
			} else {
				$stmt = null;
				return false;
			}
		} else {
			$q++;
		}

		// Set all posted plugins in array
		$plugins = array();
		foreach( $data as $plugin => $value ) {
			if( is_numeric( $plugin ) ) {
				$plugins[] = $plugin;
			}
		}

		/*
		 * Check if plugin rights need to be deleted
		 * If plugins_id from rights isn't in $plugins delete rights
		 */
		$rights = $this->rights($data['id']);
		$delete = array();
		foreach( $rights as $key => $field ) {
			if( !in_array( $field['plugins_id'], $plugins ) ) {
				$delete[] = $field['plugins_id'];
			}
		}

		// Delete rights
		if( !empty( $delete ) ) {
			$this->deleteRights($delete, $data);
		} else {
			$q++;
		}

		// Check if rights need to be added
		$add = array();
		foreach( $plugins as $key => $plugin ) {
			if( !in_array( $plugin, array_column( $rights, 'plugins_id' ) ) ) {
				$add[$plugin] = 1;
			}
		}

		// Add rights
		if( !empty( $add ) ) {
			if( $this->addRights($add, $data['id']) ) {
				$q++;
			}
		} else {
			$q++;
		}

		if( $q === 3 ) {
			return true;
		} else {
			return false;
		}
	}
}