<?php
require_once ROOT.'/vendor/autoload.php';

// Get credentials
$credentials = json_decode( file_get_contents( ROOT.'/credentials.json' ) );

$fb = new Facebook\Facebook([
	'app_id' => $credentials->facebook->app_id,
	'app_secret' => $credentials->facebook->app_secret,
	'default_graph_version' => $credentials->facebook->version,
]);

// After returning from Facebook
if( !empty( $_GET['code'] ) && !empty( $_GET['state'] ) ) {
	$helper = $fb->getRedirectLoginHelper();

	try {
		$accessToken = $helper->getAccessToken();
	} catch( Facebook\Exceptions\FacebookResponseException $e ) {
		// When Graph returns an error
		echo 'Graph returned an error: ' . $e->getMessage();
		exit;
	} catch( Facebook\Exceptions\FacebookSDKException $e ) {
		// When validation fails or other local issues
		echo 'Facebook SDK returned an error: ' . $e->getMessage();
		exit;
	}

	if( !empty( $accessToken ) ) {
		// Logged in!
		$session->set('facebook_access_token', (string) $accessToken);

		// Replace short lived access token with long lived access token
		$oAuth2Client = $fb->getOAuth2Client();
		$session->set('facebook_access_token', $oAuth2Client->getLongLivedAccessToken($session->get('facebook_access_token')));

		// Set default access token so it doesn't need to be called each time
		$fb->setDefaultAccessToken($session->get('facebook_access_token'));

		try {
			$response = $fb->get('/me?fields=first_name,last_name,id,email,picture');
			$userNode = $response->getGraphUser();
		} catch( Facebook\Exceptions\FacebookResponseException $e ) {
			// When Graph returns an error
			echo 'Graph returned an error: ' . $e->getMessage();
			exit;
		} catch( Facebook\Exceptions\FacebookSDKException $e ) {
			// When validation fails or other local issues
			echo 'Facebook SDK returned an error: ' . $e->getMessage();
			exit;
		}

		if( $user->facebookLogin($response->getDecodedBody()) ) {
			$user->to('?path=overview/overview');
		} else {
			echo 'Logging in using facebook went wrong';
		}
	}
} else {
	$helper = $fb->getRedirectLoginHelper();
	$loginUrl = $helper->getLoginUrl( 'http://www.cms.dsuper.nl/dashboard/?path=users/facebook-login' );

	$user->to( $loginUrl );
}