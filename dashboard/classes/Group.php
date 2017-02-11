<?php
class Group extends Database implements Plugin {
	public function add( array $data ) {
		$q = 0; // Store completed queries
		$stmt = $this->mysqli->prepare("INSERT INTO `groups` (`group`, `description`) VALUES (:group, :description)");
		$stmt->bindParam(':group', $data['group'], PDO::PARAM_STR);
		$stmt->bindParam(':description', $data['description'], PDO::PARAM_STR);
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

	public function data( int $id = null ) {
		if( !is_null( $id ) ) {
			$stmt = $this->mysqli->prepare( "SELECT `group`, `description` FROM `groups` WHERE `id` = :id");
			$stmt->bindParam( ':id', $id, PDO::PARAM_INT );
		} else {
			$stmt = $this->mysqli->prepare("SELECT `group`, `description` FROM `groups`");
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

	}
}