<?php
class Facebook extends \Facebook\Facebook {
	public 	$data = array(),
			$accessToken;

	private $_helper,
			$_credentials;

	function __construct() {
		// Get credentials
		$this->_credentials = json_decode( file_get_contents( ROOT.'/credentials.json' ) );

		parent::__construct([
			'app_id' => $this->_credentials->facebook->app_id,
			'app_secret' => $this->_credentials->facebook->app_secret,
			'default_graph_version' => $this->_credentials->facebook->version,
		]);

		$this->_helper = $this->getRedirectLoginHelper();
	}

	/**
	 * Generate url
	 *
	 * @param       $url
	 * @param array $permission
	 *
	 * @return string
	 */
	public function urlGenerate( string $url, array $permission = array() ) {
		if( !empty( $permission ) ) {
			return $this->_helper->getLoginUrl( $url, $permission );
		} else {
			return $this->_helper->getLoginUrl( $url );
		}
	}

	/**
	 * Return access token
	 *
	 * @return \Facebook\Authentication\AccessToken|null
	 */
	public function getAccessToken() {
		try {
			$accessToken = $this->_helper->getAccessToken();
		} catch( Facebook\Exceptions\FacebookResponseException $e ) {
			// When Graph returns an error
			echo 'Graph returned an error: ' . $e->getMessage();
			exit;
		} catch( Facebook\Exceptions\FacebookSDKException $e ) {
			// When validation fails or other local issues
			echo 'Facebook SDK returned an error: ' . $e->getMessage();
			exit;
		}

		if( !isset( $accessToken ) ) {
			if( $this->_helper->getError() ) {
				header( 'HTTP/1.0 401 Unauthorized' );
				echo "	Error: {$this->_helper->getError()}\r\n
						Error Code: {$this->_helper->getErrorCode()}\r\n
						Error Reason: {$this->_helper->getErrorReason()}\r\n
						Error Description: {$this->_helper->getErrorDescription()}\r\n";
			} else {
				header( 'HTTP/1.0 400 Bad Request' );
				echo 'Bad request';
			}
			exit;
		} else {
			return $accessToken;
		}
	}

	/**
	 * Set access token in class
	 *
	 * @param $token
	 */
	public function setAccessToken($token) {
		$this->accessToken = $token;
	}

	/**
	 * Set Facebook session
	 */
	public function login() {
		// Logged in
		/*echo '<h3>Access Token</h3>';
		var_dump($accessToken->getValue());*/

		// The OAuth 2.0 client handler helps us manage access tokens
		$oAuth2Client = $this->getOAuth2Client();

		// Get the access token metadata from /debug_token
		$tokenMetadata = $oAuth2Client->debugToken($this->accessToken);
		/*echo '<h3>Metadata</h3>';
		var_dump($tokenMetadata);*/

		// Validation (these will throw FacebookSDKException's when they fail)
		$tokenMetadata->validateAppId($this->_credentials->facebook->app_id); // Replace {app-id} with your app id
		// If you know the user ID this access token belongs to, you can validate it here
		$tokenMetadata->validateUserId($tokenMetadata->getUserId());
		$tokenMetadata->validateExpiration();

		if( !$this->accessToken->isLongLived() ) {
			// Exchanges a short-lived access token for a long-lived one
			try {
				$this->accessToken = $oAuth2Client->getLongLivedAccessToken($this->accessToken);
			} catch( Facebook\Exceptions\FacebookSDKException $e ) {
				echo "<p>Error getting long-lived access token: " . $this->_helper->getMessage() . "</p>\n\n";
				exit;
			}

			echo '<h3>Long-lived</h3>';
			var_dump($this->accessToken->getValue());
		}

		$_SESSION['facebook'] = base64_encode( (string) $this->accessToken );

		if( !empty( $_SESSION['facebook'] ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Send request to Facebook
	 *
	 * @param array $fields
	 *
	 * @return array
	 */
	public function getRequest( array $fields = array() ) {
		try {
			if( !empty( $fields ) ) {
				$response = $this->get('/me?fields='.implode(',', $fields ), $this->accessToken);
			} else {
				$response = $this->get( '/me', $this->accessToken );
			}
		} catch( Facebook\Exceptions\FacebookResponseException $e ) {
			// When Graph returns an error
			echo 'Graph returned an error: ' . $e->getMessage();
			exit;
		} catch( Facebook\Exceptions\FacebookSDKException $e ) {
			// When validation fails or other local issues
			echo 'Facebook SDK returned an error: ' . $e->getMessage();
			exit;
		}
		return $response->getDecodedBody();
	}
}