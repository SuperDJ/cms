<?php
class User extends Database {
	public $data = array(); // Store logged in user data so it isn't queried each time

	private $_id; // Store user id

	function __construct() {
		parent::__construct();

		if( $this->isLoggedIn() ) {
			$this->_id = (int)base64_decode( $_SESSION['user'] );

			// Check if user data already has been stored
			if( empty( $this->data ) ) {
				$this->data = $this->data($this->_id);
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

	public function data( $id ) {
		if( empty( $id ) ) {
			return false;
		} else {
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
	}

	/**
	 * Recover password
	 *
	 * @param array    $data 		All data required
	 * @param callable $translate	Translations for email
	 * @param string   $code		Email code
	 *
	 * @return bool
	 */
	public function recover( array $data, callable $translate, $code = '' ) {
		if( !empty( $code ) ) {
			$explode = explode( '|', base64_decode( $code ) );
			$active_date = $explode[0];
			$email = $explode[1];

			if( $email == $data['email'] && $this->detail('active_date', 'users', 'email', $data['email']) === $active_date ) {
				$password = password_hash( $this->passwordGenerate( $data['password_encrypted'] ), PASSWORD_DEFAULT );
				$active = 1;

				$stmt = $this->mysqli->prepare("UPDATE `users` SET `password` = ?, active = ? WHERE `email` = ?");
				$stmt->bind_param('sis', $password, $active, $email);
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
		} else {
			$stmt = $this->mysqli->prepare("SELECT `first_name`, `last_name`, `active_date` FROM `users` WHERE `email` = ?");
			$stmt->bind_param('s', $data['email']);
			$stmt->execute();
			$stmt->bind_result( $first_name, $last_name, $active_date );
			$stmt->fetch();

			$code = base64_encode( $active_date.'|'.$data['email'] );

			// TODO get name from database
			// TODO get url from database
			// TODO get email form database
			$subject = $translate( 'Password recovery' ).' DSuper';
			$message = $translate( 'Hello' )." ".substr( $first_name, 0, 1 )." ".$last_name.",\r\n\r\n".
				$translate( 'Go to the following link to reset your password' ).": http://www.cms.dsuper.nl/dashboard/?path=users/recover&code={$code}\r\n\r\n".
				$translate( 'Greetings' ).",\r\nDSuper";
			$header = "From: <info@dsuper.nl> DSuper\r\n";

			// Close first query
			$stmt->close();

			// Set active to 0 if necessary
			if( $this->detail('active', 'users', 'email', $data['email']) !== 0 ) {
				$active = null;
				$stmt = $this->mysqli->prepare( "UPDATE `users` SET `active` = ? WHERE `email` = ?" );
				$stmt->bind_param( 'is', $active, $data['email'] );
				$stmt->execute();

				if( $stmt->affected_rows === 0 ) {
					$stmt->close();
					return false;
				}
				$stmt->close();
			}

			if( mail( $data['email'], $subject, $message, $header ) ) {
				return true;
			} else {
				return false;
			}
		}
	}
}