<?php
class User extends Database {
	public $data = array(); // Store logged in user data so it isn't queried each time

	private $_id; // Store user id

	function __construct() {
		parent::__construct();

		if( $this->isLoggedIn() ) {
			$this->_id = base64_decode( $_SESSION['user'] );

			if( !empty( $this->_id ) ) {
				$this->data = $this->details($this->_id);
			}
		}
	}

	/**
	 * Register user
	 *
	 * @param array $data All data needed to register user
	 *
	 * @return bool
	 */
	public function register( array $data ) {
		$password = password_hash( $this->passwordGenerate( $data['password_encrypted'] ), PASSWORD_DEFAULT );
		$register_date = date('Y-m-d H:i:s');

		$stmt = $this->mysqli->prepare("INSERT INTO `users` (`first_name`, `last_name`, `email`, `password`, `register_date`) VALUES (?, ?, ?, ?, ?)");
		$stmt->bind_param('sssss', $data['first_name'], $data['last_name'], $data['email'], $password, $register_date);
		$stmt->execute();

		if( $stmt->affected_rows >= 1 ) {
			$stmt->close();
			return true;
		} else {
			$stmt->close();
			return false;
		}
	}

	/**
	 * Login user
	 *
	 * @param array $data All data needed to login user
	 *
	 * @return bool
	 */
	public function login( array $data ) {
		$password = $this->passwordGenerate( $data['password_encrypted'] );
		$hash = $this->detail('password', 'users', 'email', $data['email']);
		$date = date('Y-m-d H:i:s');

		if( password_verify( $password, $hash ) ) {
			$stmt = $this->mysqli->prepare("UPDATE `users` SET `active_date` = ? WHERE `email` = ?");
            $stmt->bind_param('ss', $date, $data['email']);
			$stmt->execute();

			if( $stmt->affected_rows >= 1 ) {
				$stmt->close();
				return true;
			} else {
				$stmt->close();
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * Login user using Facebook
	 *
	 * @param array $data
	 *
	 * @return bool
	 *
	 * TODO add profile picture, timezone, language
	 */
	public function facebookLogin( array $data ) {
		$date = date('Y-m-d H:i:s');

		// Check if email exists
		if( $this->exists('email', 'users', 'email', $data['email']) ) {
			$stmt = $this->mysqli->prepare("UPDATE `users` SET `first_name` = ?, `last_name` = ?, `active_date` = ?, `facebook_id` = ? WHERE `email` = ?");
			$stmt->bind_param("sssis", $data['first_name'], $data['last_name'], $date, $data['id'], $data['email']);
			$stmt->execute();

			if( $stmt->affected_rows >= 1 ) {
				$stmt->close();
				return true;
			} else {
				$stmt->close();
				return false;
			}
		} else {
			$stmt = $this->mysqli->prepare("INSERT INTO `users` (`first_name`, `last_name`, `email`, `register_date`, `active_date`, `active`, `facebook_id`) VALUES (?, ?, ?, ?, ?, ?, ?)");
			$stmt->bind_param('sssssii', $data['first_name'], $data['last_name'], $data['email'], $date, $date, 1, $data['id']);
			$stmt->execute();

			if( $stmt->affected_rows >= 1 ) {
				$stmt->close();
				return true;
			} else {
				$stmt->close();
				return false;
			}
		}
	}

	/**
	 * Generate a password
	 *
	 * @param $password
	 *
	 * @return string
	 */
	private function passwordGenerate( $password ) {
		return hash( 'sha512', $password );
	}

	/**
	 * Check if user is logged in
	 *
	 * @return bool
	 */
	public function isLoggedIn() {
		if( !empty( $_SESSION['user'] ) && $this->exists('email', 'users', 'id', base64_decode( $_SESSION['user'] ) ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Go to url
	 *
	 * @param  string $url The url to go to
	 */
	public function to( $url ) {
		if( headers_sent() ) {
			// For JavaScript and when JavaScript is turned off
			echo '	<script>window.location = "'.$url.'";</script>
				<noscript><meta http-equiv="refresh" content="0;url='.$url.'"></noscript>';
		} else {
			header( 'Location: '.$url );
			exit();
		}
	}

	/**
	 * Get all data from logged in user or a specific user
	 *
	 * @param int $id
	 *
	 * @return array|bool
	 */
	public function data( $id  ) {
		$id = (int)$id; // Convert to integer
		$stmt = $this->mysqli->prepare("SELECT `id`, `first_name`, `last_name`, `email`, `register_date`, `active_date` FROM `users` WHERE `id` = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$stmt->bind_result($userID, $first_name, $last_name, $email, $register_date, $active_date);

		// Set all data in array
		$data = array();
		while( $stmt->fetch() ) {
			$data['id'] = $userID;
			$data['first_name'] = $first_name;
			$data['last_name'] = $last_name;
			$data['email'] = $email;
			$data['register_date'] = $register_date;
			$data['active_date'] = $active_date;
		}

		if( !empty( $data ) ) {
			$stmt->close();
			return $data;
		} else {
			$stmt->close();
			return false;
		}
	}

	/**
	 * Edit user
	 *
	 * @param array $data
	 *
	 * @return bool
	 */
	public function edit( array $data ) {
		$stmt = $this->mysqli->prepare("UPDATE `users` SET `first_name` = ?, `last_name` = ?, `email` = ? WHERE `id` = ?");
		$stmt->bind_param('sssi', $data['first_name'], $data['last_name'], $data['email'], $this->_id);
		$stmt->execute();

		if( $stmt->affected_rows >= 1 ) {
			$stmt->close();
			return true;
		} else {
			$stmt->close();
			return false;
		}
	}
}