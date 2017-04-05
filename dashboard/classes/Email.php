<?php
class Email  implements Plugin {
	private $_db;

	function __construct( Database $db = null ) {
		if( !is_null( $db ) ) {
			$this->_db = $db;
		}
	}

	/**
	 * Add email to database and send email
	 *
	 * @param array $data
	 *
	 * @return bool
	 */
	public function add( array $data ) {
		// Insert email in database
		$user = $_SESSION['user']['id'];
		$stmt = $this->_db->mysqli->prepare("INSERT INTO `emails` (`subject`, `content`, `send_by`, `languages_id`, `to`) VALUES (:subject, :content, :send_by, :languages_id, :to)");
		$stmt->bindParam(':subject', $data['subject'], PDO::PARAM_STR);
		$stmt->bindParam(':content', $data['content'], PDO::PARAM_STR);
		$stmt->bindParam(':send_by', $user, PDO::PARAM_INT);
		$stmt->bindParam(':languages_id', $data['language'], PDO::PARAM_INT);
		$stmt->bindParam(':to', $data['to'], PDO::PARAM_INT);
		$stmt->execute();

		if( $stmt->rowCount() >= 1 ) {
			$stmt = null;

			// Get user data and create "$headers"
			$stmt = $this->_db->mysqli->prepare("SELECT `first_name`, `last_name`, `email` FROM `users` WHERE `id` = :id");
			$stmt->bindParam(':id', $user, PDO::PARAM_INT);
			$stmt->execute();

			if( $stmt->rowCount() >= 1 ) {
				$result = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
				$stmt = null;
				$headers = 'From: '.substr( $result['first_name'], 0, 1 ).' '.$result['last_name'].' <'.$result['email'].'>';
			} else {
				$stmt = null;
				return false;
			}

			// If empty "$data['to']" send batch of emails else send to one
			if( empty( $data['to'] ) ) {
				$stmt = $this->_db->mysqli->prepare("SELECT `email` FROM `users` WHERE `languages_id` = :languages_id");
				$stmt->bindParam(':languages_id', $data['languages_id'], PDO::PARAM_INT);
				$stmt->execute();

				if( $stmt->rowCount() >= 1 ) {
					$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
					$stmt = null;

					$count = count( $result );
					$i = 0;
					foreach( $result as $row => $field ) {
						if( mail($field['email'], $data['subject'], $data['content'], $headers ) ) {
							$i++;
						} else {
							return false;
						}
					}

					if( $count === $i ) {
						return true;
					} else {
						return false;
					}
				} else {
					$stmt = null;
					return false;
				}
			} else {
				$to = $this->_db->detail('email', 'users', 'id', $data['to']);
				if( mail( $to, $data['subject'], $data['content'], $headers ) ) {
					return true;
				} else {
					return false;
				}
			}
		} else {
			$stmt = null;
			return false;
		}
	}

	/**
	 * Does nothing since an email cant be edited once send
	 *
	 * @param array $data
	 *
	 * @return bool
	 */
	public function edit( array $data ) {
		return false;
	}

	/**
	 * Delete email
	 *
	 * @param int $id
	 *
	 * @return bool
	 */
	public function delete( int $id ) {
		$stmt = $this->_db->mysqli->prepare("DELETE FROM `emails` WHERE `id` = :id");
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
	 * Get email data
	 *
	 * @param int|null $id
	 *
	 * @return bool
	 */
	public function data( int $id = null ) {
		if( !is_null( $id ) ) {
			$stmt = $this->_db->mysqli->prepare("
				SELECT `e`.`id`, `subject`, `content`, `send_by`, `u`.`email`, `read`, `e`.`languages_id`, `language`, `to`
				FROM `emails` `e`
				JOIN `users` `u`
					ON `e`.`send_by` = `u`.`id`
				JOIN `languages` `l`
					ON `e`.`languages_id` = `l`.`id`
				WHERE `e`.`id` = :id
			");
			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		} else {
			$stmt = $this->_db->mysqli->prepare( "
				SELECT `e`.`id`, `subject`, `content`, `send_by`, `u`.`email`, `read`, `e`.`languages_id`, `language`, `to`
				FROM `emails` `e`
				LEFT JOIN `users` `u`
					ON `e`.`send_by` = `u`.`id`
				LEFT JOIN `languages` `l`
					ON `e`.`languages_id` = `l`.`id`
			" );
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
