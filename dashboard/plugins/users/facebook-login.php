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

		// Set Facebook session
		$fb->login();

		// Request user information
		$data = $fb->getRequest([ 'id', 'picture' ]);

		//print_r($data);
		$sess =  $session->exists('facebook');
		$log = $user->facebookLogin($data);
		echo $sess.' '.$log;
		if( $log && $sess ) {
			$user->to('?path=overview/overview&message='.$language->translate('Facebook logged in').'&messageType=success');
		} else {
			echo '	<div class="error sc-card sc-card-supporting-additional" role="alert">
						'.$language->translate('Could not login to Facebook').'
					</div>';
		}
	}
}