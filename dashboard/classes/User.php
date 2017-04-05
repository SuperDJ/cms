<?php
class User implements Plugin {
	public $data = array(); // Store logged in user data so it isn't queried each time

	private $_id, // Store user id
			$_db;

	function __construct( Database $db = null ) {
		if( !is_null( $db ) ) {
			$this->_db = $db;
		}

		if( $this->isLoggedIn() ) {
			$this->_id = (int)$_SESSION['user']['id'];

			// Check if user data already has been stored
			if( empty( $this->data ) ) {
				$this->data = $this->data($this->_id)[0];
			}
		}
	}

	/**
	 * Add new user
	 * TODO Send email to confirm new user
	 * @param array $data
	 *
	 * @return bool
	 */
	public function add( array $data ) {
		$register_date = date('Y-m-d H:i:s');
		$active = 1;

		$stmt = $this->_db->mysqli->prepare("INSERT INTO `users` (`first_name`, `last_name`, `email`, `register_date`, `active`, `groups_id`) VALUES (:first_name, :last_name, :email, :register_date, :active, :groups_id)");
		$stmt->bindParam(':first_name', $data['first_name'], PDO::PARAM_STR);
		$stmt->bindParam(':last_name', $data['last_name'], PDO::PARAM_STR);
		$stmt->bindParam(':email', $data['email'], PDO::PARAM_STR);
		$stmt->bindParam(':register_date', $register_date, PDO::PARAM_STR);
		$stmt->bindParam(':active', $active, PDO::PARAM_INT);
		$stmt->bindParam(':groups_id', $data['group'], PDO::PARAM_STR);
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
	 * Register user
	 *
	 * @param array $data All data needed to register user
	 *
	 * @return bool
	 */
	public function register( array $data ) {
		$password = password_hash( $this->passwordGenerate( $data['password_encrypted'] ), PASSWORD_DEFAULT );
		$register_date = date('Y-m-d H:i:s');
		$groups_id = $this->_db->detail('id', 'groups', 'default', 1);
		$groups_id = ( !empty( $groups_id ) ? $groups_id : 0 );

		$stmt = $this->_db->mysqli->prepare("INSERT INTO `users` (`first_name`, `last_name`, `email`, `password`, `register_date`, `groups_id`) VALUES (:first_name, :last_name, :email, :password, :register_date, :groups_id)");
		$stmt->bindParam(':first_name', $data['first_name'], PDO::PARAM_STR);
		$stmt->bindParam(':last_name', $data['last_name'], PDO::PARAM_STR);
		$stmt->bindParam(':email', $data['email'], PDO::PARAM_STR);
		$stmt->bindParam(':password', $password, PDO::PARAM_STR);
		$stmt->bindParam(':register_date', $register_date, PDO::PARAM_STR);
		$stmt->bindParam(':groups_id', $groups_id, PDO::PARAM_INT);
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
	 * Login user
	 *
	 * @param array $data All data needed to login user
	 *
	 * @return bool
	 */
	public function login( array $data ) {
		// Check if user is activated
		$stmt = $this->_db->mysqli->prepare("SELECT `id` FROM `users` WHERE `active` = 1 AND `email` = :email");
		$stmt->bindParam(':email', $data['email'], PDO::PARAM_STR);
		$stmt->execute();

		if( $stmt->rowCount() >= 1 ) {
			$stmt = null; // Close query
			$password = $this->passwordGenerate( $data['password_encrypted'] );
			$hash = $this->_db->detail( 'password', 'users', 'email', $data['email'] );
			$date = date( 'Y-m-d H:i:s' );

			if( password_verify( $password, $hash ) ) {
				// Check if better password methods are available
				if( password_needs_rehash( $hash, PASSWORD_DEFAULT ) ) {
					// Set rehashed password
					$newHash = password_hash( $this->passwordGenerate( $data['password_encrypted'] ), PASSWORD_DEFAULT );
					$stmt = $this->_db->mysqli->prepare( "UPDATE `users` SET `password` = :password WHERE `email` = :email" );
					$stmt->bindParam( ':password', $newHash, PDO::PARAM_STR );
					$stmt->bindParam( ':email', $data['email'], PDO::PARAM_STR );
					$stmt->execute();

					if( $stmt->rowCount() >= 1 ) {
						$stmt = null;
					} else {
						$stmt = null;
						return false;
					}
				}

				// Login user
				$stmt = $this->_db->mysqli->prepare( "UPDATE `users` SET `active_date` = :active_date WHERE `email` = :email" );
				$stmt->bindParam( ':active_date', $date, PDO::PARAM_STR );
				$stmt->bindParam( ':email', $data['email'], PDO::PARAM_STR );
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
	private function passwordGenerate( string $password ) {
		return hash( 'sha512', $password );
	}

	/**
	 * Check if user is logged in
	 *
	 * @return bool
	 */
	public function isLoggedIn() {
		if( !empty( $_SESSION['user']['id'] ) && $this->_db->exists('email', 'users', 'id', $_SESSION['user']['id'] ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Check if user has permission for certain file/ plugin
	 *
	 * @param $path
	 *
	 * @return bool
	 */
	public function hasPermission( string $path ) {
		if( empty( $_SESSION['user'] ) ) {
			return false;
		}

		$group = $_SESSION['user']['group'];
		if( !empty( $_SESSION['user'] ) && $this->_db->exists('id', 'groups', 'id', $group ) ) {
			$stmt = $this->_db->mysqli->prepare("
				SELECT `url` FROM `plugins` `p`
				JOIN `rights` `r`
					ON `r`.`plugins_id` = `p`.`id`
				WHERE `r`.`groups_id` = :groups_id
			");
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
	public function to( string $url ) {
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
	 * Get data from users
	 *
	 * @param int|null $id
	 *
	 * @return bool
	 */
	public function data( int $id = null ) {
		$query = "
				SELECT 	`u`.`id`, `first_name`, `last_name`, `email`, `register_date`, `active_date`, `active`,
						`groups_id`, `group`, `facebook_id`, `languages_id`, `language`, `picture` 
				FROM `users` `u`
			  	LEFT JOIN `groups` `g`
					ON `g`.`id` = `u`.groups_id
				LEFT JOIN `languages` `l`
					ON `l`.`id` = `u`.languages_id";
		if( !is_null( $id ) ) {
			$query .= "	WHERE `u`.`id` = :id	
						LIMIT 1";
			$stmt = $this->_db->mysqli->prepare($query);
			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		} else {
			$stmt = $this->_db->mysqli->prepare($query);
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

	/**
	 * Edit a users information
	 *
	 * @param array $data
	 *
	 * @return bool
	 */
	public function edit( array $data ) {
		$stmt = $this->_db->mysqli->prepare("UPDATE `users` SET `active` = :active, `groups_id` = :groups_id WHERE `id` = :id ");
		$stmt->bindParam(':active', $data['active'], PDO::PARAM_INT);
		$stmt->bindParam(':groups_id', $data['group'], PDO::PARAM_INT);
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
	 * Delete a user from the database
	 *
	 * @param int $id
	 *
	 * @return bool
	 */
	public function delete( int $id ) {
		$stmt = $this->_db->mysqli->prepare("DELETE FROM `users` WHERE `id` = :id");
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

			if( $email == $data['email'] && $this->_db->detail('active_date', 'users', 'email', $data['email']) === $active_date ) {
				$password = password_hash( $this->passwordGenerate( $data['password_encrypted'] ), PASSWORD_DEFAULT );
				$active = 1;

				$stmt = $this->_db->mysqli->prepare("UPDATE `users` SET `password` = :password, active = :active WHERE `email` = :email");
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
			$stmt = $this->_db->mysqli->prepare("SELECT `first_name`, `last_name`, `active_date` FROM `users` WHERE `email` = :email LIMIT 1");
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
			if( $this->_db->detail('active', 'users', 'email', $data['email']) !== 0 ) {
				$active = null;
				$stmt = $this->_db->mysqli->prepare( "UPDATE `users` SET `active` = :active WHERE `email` = :email" );
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

	/**
	 * Let a user edit their profile
	 *
	 * @param array $data
	 *
	 * @return bool
	 */
	public function profile( array $data ) {
		$stmt = $this->_db->mysqli->prepare("
			UPDATE `users` SET 
				`first_name` = :first_name,
				`last_name` = :last_name,
				`email` = :email,
				`languages_id` = :languages_id
			WHERE `id` = :id");

		$stmt->bindParam(':first_name', $data['first_name'], PDO::PARAM_STR);
		$stmt->bindParam(':last_name', $data['last_name'], PDO::PARAM_STR);
		$stmt->bindParam(':email', $data['email'], PDO::PARAM_STR);
		$stmt->bindParam(':languages_id', $data['language'], PDO::PARAM_INT);
		$stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
		$stmt->execute();

		if( $stmt->rowCount() >= 1 ) {
			$stmt = null;

			// Update user data
			$this->data = $this->data($this->_id);

			return true;
		} else {
			$stmt = null;
			return false;
		}
	}

	public function facebookLogin( array $data ) {
		$facebook = $data['id'];
		$image = ( !empty( $data['picture']['data']['url'] ) ? $data['picture']['data']['url'] : '' );

		// Check if values have changed
		if( $facebook == $this->data['facebook_id'] && $image == $this->data['picture'] ) {
			return true;
		}

		$stmt = $this->_db->mysqli->prepare("UPDATE `users` SET `facebook_id` = :facebook_id, `picture` = :picture WHERE `id` = :id");
		$stmt->bindParam(':facebook_id', $facebook, PDO::PARAM_INT);
		$stmt->bindParam(':picture', $image, PDO::PARAM_STR);
		$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
		$stmt->execute();

		if( $stmt->rowCount() >= 1 ) {
			$stmt = null;
			return true;
		} else {
			$stmt = null;
			return false;
		}
	}
}