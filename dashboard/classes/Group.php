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

		// Create query to insert all plugins for group
		$query = "INSERT INTO `rights` (`groups_id`, `plugins_id`) VALUES ";
		$insertQuery = array();
		$insertData = array();

		// $value is always 1
		foreach( $data as $plugin_id => $value ) {
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
				$q++;
			} else {
				$stmt = null;
				return false;
			}
		}

		// If both queries executed
		if( $q === 2 ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Delete group from database
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
	 * Get data from database
	 * @param int|null $id
	 *
	 * @return bool
	 */
	public function data( int $id = null ) {
		if( !is_null( $id ) ) {
			$stmt = $this->mysqli->prepare("
				SELECT `g`.`id`, `group`, `default`, `description`, concat( round( ( COUNT(`r`.`id`) / `plugins` ) * 100 ), '%' ) as `rights`
				FROM `groups` `g`
				JOIN (
					SELECT COUNT(`id`) `plugins`
					FROM `plugins`
					) `p`
				JOIN `rights` `r`
				  ON `r`.`groups_id` = `g`.`id`
				WHERE `g`.`id` = :id
				GROUP BY `g`.`id`");
			$stmt->bindParam( ':id', $id, PDO::PARAM_INT );
		} else {
			$stmt = $this->mysqli->prepare("
				SELECT `g`.`id`, `group`, `default`, `description`, concat( round( ( COUNT(`r`.`id`) / `plugins` ) * 100 ), '%' ) as `rights`
				FROM `groups` `g`
				JOIN (
					SELECT COUNT(`id`) `plugins`
					FROM `plugins`
					) `p`
				JOIN `rights` `r`
				  ON `r`.`groups_id` = `g`.`id`
				GROUP BY `g`.`id`");
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

	public function rights( int $id ) {
		$stmt = $this->mysqli->prepare("SELECT `groups_id`, `plugins_id` FROM `rights` WHERE `groups_id` = :id");
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

	public function edit( array $data ) {
		$q = 0; // Store query success
		// Check if description or group needs to be update
		if( $data['description'] != $this->detail('description', 'groups', 'id', $data['id']) ||
			$data['group'] != $this->detail('group', 'groups', 'id', $data['id']) ||
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

		// Check if rights need to be added
		$add = array();
		foreach( $plugins as $key => $plugin ) {
			if( !in_array( $plugin, array_column( $rights, 'plugins_id' ) ) ) {
				$add[] = $plugin;
			}
		}

		// Delete rights
		if( !empty( $delete ) ) {
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
				$q++;
			} else {
				return false;
			}
		} else {
			$q++;
		}

		// Add rights
		if( !empty( $add ) ) {
			$stmt = $this->mysqli->prepare("INSERT INTO `rights` (`groups_id`, `plugins_id`) VALUES (:groups_id, :plugins_id) ");

			$count = count( $add );
			$i = 0;
			foreach( $add as $key => $plugin ) {
				$stmt->bindParam(':groups_id', $data['id'], PDO::PARAM_INT);
				$stmt->bindParam(':plugins_id', $plugin, PDO::PARAM_INT);
				$stmt->execute();

				if( $stmt->rowCount() >= 1 ) {
					$i++;
				} else {
					return false;
				}
			}

			$stmt = null;
			if( $count === $i ) {
				$q++;
			} else {
				return false;
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