<?php
class Page extends Database implements Plugin {

	public function add( array $data ) {
		// TODO: Implement add() method.
	}

	public function edit( array $data ) {
		// TODO: Implement edit() method.
	}

	/**
	 * Delete page from database
	 *
	 * @param int $id
	 *
	 * @return bool
	 */
	public function delete( int $id ) {
		$stmt = $this->mysqli->prepare("DELETE FROM `pages` WHERE `id` = :id");
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
		// TODO: Implement data() method.
	}
}