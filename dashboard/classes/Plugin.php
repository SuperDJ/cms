<?php
class Plugin extends Database {

	public function add() {}

	public function delete() {}

	public function edit() {}

	public function data( $query, array $columns ) {
		$stmt = $this->mysqli->prepare($query);
		foreach( $columns as $field => $value ) {
			switch( )
		}
	}
}