<?php
class Users extends Database implements Plugin {
	public function add() {

	}

	public function edit() {

	}

	public function delete() {

	}

	public function data( $id = null ) {
		if( is_null( $id ) ) {
			$stmt = $this->mysqli->prepare( "SELECT `id`, `first_name`, `last_name`, `email`, `register_date`, `active_date`, `active` FROM `users`" );
		} else {
			$stmt = $this->mysqli->prepare( "SELECT `id`, `first_name`, `last_name`, `email`, `register_date`, `active_date`, `active` FROM `users` WHERE `id` = :id" );
			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		}
		$stmt->execute();

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
}