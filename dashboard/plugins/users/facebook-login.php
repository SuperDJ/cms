<?php
if( !$user->isLoggedIn() && !$user->hasPermission($path) ) {
	$user->to('?path=overview/overview');
} else {

	// Request permission
	if( empty( $_GET['code'] ) && empty( $_GET['state'] ) ) {
		//$user->to( $helper->getLoginUrl( 'https://cms.dsuper.nl/dashboard/?path=users/facebook-login', [ 'email', 'user_likes' ] ) );
		$user->to( $fb->urlGenerate( 'https://cms.dsuper.nl/dashboard/?path=users/facebook-login', [ 'email', 'user_likes' ] ) );
	} else {
		$accessToken = $fb->getAccessToken();

		if( !empty( $accessToken ) ) {
			$fb->setAccessToken($accessToken);
		} else {
			echo 'Something went wrong with logging in into Facebook';
		}

		$fb->login();

		$data = $fb->getRequest([ 'id', 'first_name', 'last_name', 'email', 'languages', 'picture' ]);

		print_r($data);
		//$user->facebookLogin($data);
	}
}