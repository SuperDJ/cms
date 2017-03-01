<?php
interface Plugin {
	/**
	 * Add something to database
	 *
	 * @param array $data
	 *
	 * @return mixed
	 */
	public function add( array $data );

	/**
	 * Delete something from database
	 *
	 * @param int $id
	 *
	 * @return mixed
	 */
	public function delete( int $id );

	/**
	 * Edit something in database
	 *
	 * @param array $data
	 *
	 * @return mixed
	 */
	public function edit( array $data );

	/**
	 * Get data from database
	 *
	 * @param int|null $id
	 *
	 * @return mixed
	 */
	public function data( int $id = null );
}