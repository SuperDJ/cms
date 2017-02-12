<?php
class User extends Database implements Plugin {
	public $data = array(); // Store logged in user data so it isn't queried each time

	private $_id; // Store user id

	function __construct() {
		parent::__construct();

		if( $this->isLoggedIn() ) {
			$this->_id = (int)$_SESSION['user']['id'];

			// Check if user data already has been stored
			if( empty( $this->data ) ) {
				$this->data = $this->data($this->_id);
			}
		}
	}

	/**
	 * Add/ Register user
	 *
	 * @param array $data All data needed to register user
	 *
	 * @return bool
	 */
	public function add( array $data ) {
		$password = password_hash( $this->passwordGenerate( $data['password_encrypted'] ), PASSWORD_DEFAULT );
		$register_date = date('Y-m-d H:i:s');

		$stmt = $this->mysqli->prepare("INSERT INTO `users` (`first_name`, `last_name`, `email`, `password`, `register_date`) VALUES (:first_name, :last_name, :email, :password, :register_date)");
		$stmt->bindParam(':first_name', $data['first_name'], PDO::PARAM_STR);
		$stmt->bindParam(':last_name', $data['last_name'], PDO::PARAM_STR);
		$stmt->bindParam(':email', $data['email'], PDO::PARAM_STR);
		$stmt->bindParam(':password', $password, PDO::PARAM_STR);
		$stmt->bindParam(':register_date', $register_date, PDO::PARAM_STR);
		$stmt->execute();

		if( $stmt->countRows() >= 1 ) {
			$stmt = null;
			return true;
		} else {
			$stmt = null;
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
			$stmt = $this->mysqli->prepare("UPDATE `users` SET `active_date` = :active_date WHERE `email` = :email");
			$stmt->bindParam(':active_date', $date, PDO::PARAM_STR);
			$stmt->bindParam(':email', $data['email'], PDO::PARAM_STR);
			$stmt->execute();

			if( $stmt->rowCount() >= 1 ) {
				$stmt = null;
				return true;
			} else {
				$stmt = null;
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
		if( !empty( $_SESSION['user']['id'] ) && $this->exists('email', 'users', 'id', $_SESSION['user']['id'] ) ) {
			return true;
		} else {
			return false;
		}
	}

	public function hasPermission( $path ) {
		$group = $_SESSION['user']['group'];
		if( !empty( $_SESSION['user'] ) && $this->exists('id', 'groups', 'id', $group ) ) {
			$stmt = $this->mysqli->prepare("SELECT `url` FROM `plugins` `p`
											JOIN `rights` `r`
											ON `r`.`plugins_id` = `p`.`id`
											WHERE `r`.`groups_id` = :groups_id");
			$stmt->bindParam(':groups_id', $group, PDO::PARAM_INT);
			$stmt->execute();

			if( $stmt->rowCount() >= 1 ) {
				$result = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'url');

				$stmt = null;
				if( in_array( $path, $result ) ) {
					return true;
				} else {
					return false;
				}
			} else {
				$stmt = null;
				return false;
			}
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

	public function data( int $id = null) {
		if( empty( $id ) ) {
			return false;
		} else {
			$stmt = $this->mysqli->prepare("SELECT `id`, `first_name`, `last_name`, `email`, `register_date`, `active_date`, `languages_id` FROM `users` WHERE `id` = :id");
			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
			$stmt->execute();

			if( $stmt->rowCount() >= 1 ) {
				return $stmt->fetch(PDO::FETCH_ASSOC);
			} else {
				return false;
			}
		}
	}

	public function edit( array $data ) {
		$stmt = $this->mysqli->prepare("UPDATE `users` SET `active` = :active, `groups_id` = :groups_id");
		$stmt->bindParam(':active', $data['active'], PDO::PARAM_INT);
		$stmt->bindParam(':groups_id', $data['groups_id'], PDO::PARAM_INT);
		$stmt->execute();

		if( $stmt->rowCount() >= 1 ) {
			$stmt = null;
			return true;
		} else {
			$stmt = null;
			return false;
		}
	}

	public function delete( int $id ) {
		$stmt = $this->mysqli->prepare("DELETE FROM `users` WHERE `id` = :id");
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

				$stmt = $this->mysqli->prepare("UPDATE `users` SET `password` = :password, active = :active WHERE `email` = :email");
				$stmt->bindParam(':password', $password, PDO::PARAM_STR);
				$stmt->bindParam(':active', $active, PDO::PARAM_INT);
				$stmt->bindParam(':email', $data['email'], PDO::PARAM_STR);
				$stmt->execute();

				if( $stmt->rowCount() >= 1 ) {
					$stmt = null;
					return true;
				} else {
					$stmt = null;
					return false;
				}
			} else {
				return false;
			}
		} else {
			$stmt = $this->mysqli->prepare("SELECT `first_name`, `last_name`, `active_date` FROM `users` WHERE `email` = :email");
			$stmt->bindParam(':email', $data['email'], PDO::PARAM_STR);
			$stmt->execute();
			$stmt->fetch();

			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$code = base64_encode( $result['active_date'].'|'.$data['email'] );

			// TODO get name from database
			// TODO get url from database
			// TODO get email form database
			$subject = $translate( 'Password recovery' ).' DSuper';
			$message = $translate( 'Hello' )." ".substr( $result['first_name'], 0, 1 )." ".$result['last_name'].",\r\n\r\n".
				$translate( 'Go to the following link to reset your password' ).": http://www.cms.dsuper.nl/dashboard/?path=users/recover&code={$code}\r\n\r\n".
				$translate( 'Greetings' ).",\r\nDSuper";
			$header = "From: <info@dsuper.nl> DSuper\r\n";

			// Close first query
			$stmt = null;

			// Set active to 0 if necessary
			if( $this->detail('active', 'users', 'email', $data['email']) !== 0 ) {
				$active = null;
				$stmt = $this->mysqli->prepare( "UPDATE `users` SET `active` = :active WHERE `email` = :email" );
				$stmt->bindParam(':active', $active, PDO::PARAM_NULL);
				$stmt->bindParam(':email', $data['email'], PDO::PARAM_STR);
				$stmt->execute();

				if( $stmt->rowCount() === 0 ) {
					$stmt = null;
					return false;
				}
				$stmt = null;
			}

			if( mail( $data['email'], $subject, $message, $header ) ) {
				return true;
			} else {
				return false;
			}
		}
	}

	public function profile( array $data ) {
		$stmt = $this->mysqli->prepare("
			UPDATE `users` SET 
				`first_name` = :first_name,
				`last_name` = :last_name,
				`email` = :email,
				`languages_id` = :languages_id
			WHERE `id` = :id");

		if(!$stmt){
			print_r($this->mysqli->errorInfo());
		}
		$stmt->bindParam(':first_name', $data['first_name'], PDO::PARAM_STR);
		$stmt->bindParam(':last_name', $data['last_name'], PDO::PARAM_STR);
		$stmt->bindParam(':email', $data['email'], PDO::PARAM_STR);
		$stmt->bindParam(':languages_id', $data['languages_id'], PDO::PARAM_INT);
		$stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
		$stmt->execute();
		if(!$stmt){
			print_r($this->mysqli->errorInfo());
		}

		if( $stmt->rowCount() >= 1 ) {
			$stmt = null;
			return true;
		} else {
			$stmt = null;
			return false;
		}
	}
}