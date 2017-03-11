<?php
class Media extends Database implements Plugin {
	/**
	 * Add media to database
	 *
	 * @param array $data
	 *
	 * @return bool
	 */
	public function add( array $data ) {
		$keys = array_keys($data);
		$files = count( $data[$keys[0]]['name'] );

		$stmt = $this->mysqli->prepare("INSERT INTO `files` (`path`, `mime`, `upload_date`) VALUES (:path, :mime, :upload_date)");
		for( $i = 0; $i < $files; $i++ ) {
			// Set file properties in variables
			$name = $data[$keys[0]]['name'][$i];
			$type = $data[$keys[0]]['type'][$i];
			$tmp = $data[$keys[0]]['tmp_name'][$i];
			$size = $data[$keys[0]]['size'][$i];
			$path = '/dashboard/uploads/'.$name;
			$date = date( 'Y-m-d H:i:s' );

			// Bind params
			$stmt->bindParam(':path', $path, PDO::PARAM_STR);
			$stmt->bindParam(':mime', $type, PDO::PARAM_STR);
			$stmt->bindParam(':upload_date', $date, PDO::PARAM_STR);
			$stmt->execute();

			// Upload and check db insertion
			if( move_uploaded_file( $tmp, $_SERVER['DOCUMENT_ROOT'].$path ) && $stmt->rowCount() >= 1 ) {
				$u++;
			} else {
				return false;
			}
		}

		$stmt = null;

		if( $u === $files ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Delete media from database
	 *
	 * @param int $id
	 *
	 * @return bool
	 */
	public function delete( int $id ) {
		$path = $this->detail('path', 'files', 'id', $id);
		$stmt = $this->mysqli->prepare("DELETE FROM `files` WHERE `id` = :id");
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		$stmt->execute();

		if( $stmt->rowCount() >= 1 && unlink( $path ) ) {
			$stmt = null;
			return true;
		} else {
			$stmt = null;
			return false;
		}
	}

	/**
	 * Edit description and title in database
	 *
	 * @param array $data
	 *
	 * @return bool
	 */
	public function edit( array $data ) {
		$stmt = $this->mysqli->prepare("UPDATE `files` SET `title` = :title, `description` = :description WHERE `id` = :id");
		$stmt->bindParam(':title', $data['title'], PDO::PARAM_STR);
		$stmt->bindParam(':description', $data['description'], PDO::PARAM_STR);
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

	/**
	 * Get all media from database
	 * @param int|null $id
	 *
	 * @return bool
	 */
	public function data( int $id = null ) {
		if( !is_null( $id ) ) {
			$stmt = $this->mysqli->prepare("SELECT `id`, `path`, `mime`, `upload_date`, `title`, `description` FROM `files` WHERE `id` = :id LIMIT 1");
			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		} else {
			$stmt = $this->mysqli->prepare("SELECT `id`, `path`, `mime`, `upload_date`, `title`, `description` FROM `files`");
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
}