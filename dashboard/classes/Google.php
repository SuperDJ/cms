<?php
class Google extends Google_Client {
	private $_credentials;

	function __construct() {
		// Get credentials
		$this->_credentials = json_decode( file_get_contents( ROOT.'/credentials.json' ) );

		// Make sure the parent construct runs
		parent::__construct();

		// Set client credentials
		$this->setClientId($this->_credentials->google->client_id);
		$this->setClientSecret($this->_credentials->google->api_key);
		$this->setRedirectUri('https://cms.dsuper.nl/dashboard/?path=users/google-login');
		$this->setScopes('email');
	}

	/**
	 * Check if user is logged in
	 *
	 * @return bool
	 */
	public function isLoggedIn() {
		if( isset( $_SESSION['google'] ) && !empty( $_SESSION['google'] ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Create login url
	 *
	 * @return string
	 */
	public function getAuthUrl() {
		return $this->createAuthUrl();
	}

	/**
	 * Check if redirect code is oke
	 *
	 * @return bool
	 */
	public function checkRedirectCode() {
		if( !empty( $_GET['code'] ) ) {
			$this->authenticate($_GET['code']);
			$token = $this->getAccessToken();
			$this->setToken($token);

			print_r($this->getPayload($token));
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Set access token/ Login user
	 * @param array $token
	 */
	private function setToken( array $token ) {
		$_SESSION['google'] = $token;
		$this->setAccessToken($token);
	}

	/**
	 * Logout from Google
	 */
	public function logout() {
		unset( $_SESSION['google'] );

		if( empty( $_SESSION['google'] ) ) {
			return true;
		} else {
			return false;
		}
	}

	public function getPayload( array $token ) {
		$ticket = $this->verifyIdToken( $token );
		$payload = $ticket->getAttributes()['payload'];
		return $payload;
	}
}