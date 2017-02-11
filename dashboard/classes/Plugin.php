<?php
interface Plugin {
	public function add( array $data );

	public function delete( int $id );

	public function edit( array $data );

	public function data( int $id = null );
}