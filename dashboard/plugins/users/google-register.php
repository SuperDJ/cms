<?php
if( !$user->isLoggedIn() && !$user->hasPermission($path) ) {
	$user->to('?path=overview/overview');
} else {
	if( !empty( $_GET['code'] ) ) {
		$google->authenticate($_GET['code']);
		$session->set('google', $google->getAccessToken());
		$user->to('?path=users/google-register');
	}

	if( $session->exists('google') ) {
		$google->setAccessToken($session->get('google'));
	}

	if( $google->getAccessToken() ) {
		$oauth = new Google_Service_Oauth2($google);
		//Get user profile data from google
		$data = $oauth->userinfo->get();

		if( $user->googleRegister($data) ) {
			$user->to('?path=overview/overview&message='.$language->translate('Google logged in').'&messageType=success');
		} else {
			$user->to('?path=overview/overview&message='.$language->translate('Google not logged in').'&messageType=error');
		}
	} else {
		$user->to( $google->createAuthUrl() );
	}
}