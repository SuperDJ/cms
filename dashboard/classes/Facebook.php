<?php
class Facebook extends \Facebook\Facebook {
	public 	$data = array();
	private $_app_id,
			$_app_secret,
			$_version;

	function __construct() {
		// Get credentials
		$credentials = json_decode( file_get_contents( ROOT.'/credentials.json' ) );
		$this->_app_id = $credentials->facebook->app_id;
		$this->_app_secret = $credentials->facebook->app_secret;
		$this->_version = $credentials->facebook->version;

		parent::__construct([
			'app_id' => $this->_app_id,
			'app_secret' => $this->_app_secret,
			'default_graph_version' => $this->_version,
		]);
	}

	/**
	 * Generate url
	 *
	 * @param       $url
	 * @param array $permission
	 *
	 * @return string
	 */
	public function urlGenerate( $url, array $permission = array() ) {
		$helper = $this->getRedirectLoginHelper();

		if( !empty( $permission ) ) {
			return $helper->getLoginUrl( $url, $permission );
		} else {
			return $helper->getLoginUrl( $url );
		}
	}

	/**
	 * Check access token
	 *
	 * @return \Facebook\Authentication\AccessToken|null
	 */
	public function accessToken() {
		$helper = $this->getRedirectLoginHelper();

		try {
			$accessToken = $helper->getAccessToken();
			echo $accessToken;
		} catch( Facebook\Exceptions\FacebookResponseException $e ) {
			// When Graph returns an error
			echo 'Graph returned an error: ' . $e->getMessage();
			echo 1;
			exit;
		} catch( Facebook\Exceptions\FacebookSDKException $e ) {
			// When validation fails or other local issues
			echo 'Facebook SDK returned an error: ' . $e->getMessage();
			exit;
		}

		return $accessToken;
	}

	/**
	 * Set a default access token
	 *
	 * @return bool
	 */
	public function setAccessToken() {
		// Logged in!
		$_SESSION['facebook'] = $this->accessToken();

		// Replace short lived access token with long lived access token
		$oAuth2Client = $this->getOAuth2Client();
		$_SESSION['facebook'] =  $oAuth2Client->getLongLivedAccessToken($_SESSION['facebook']);

		// Set default access token so it doesn't need to be called each time
		$this->setDefaultAccessToken($_SESSION['facebook']);

		if( !empty( $_SESSION['facebook'] ) ) {
			return true;
		} else {
			return false;
		}
	}

	public function getRequest( array $fields = array() ) {
		try {
			if( !empty( $fields ) ) {
				$response = $this->get('/me?fields='.implode(',', $fields ));
			} else {
				$response = $this->get( '/me' );
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

	public function logout() {
		if( !empty( $_SESSION['facebook'] ) ) {
			unset( $_SESSION['facebook'] );

			if( empty( $_SESSION['facebook'] ) ) {
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}
}