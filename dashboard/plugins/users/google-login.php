<?php
if( $user->isLoggedIn() ) {
	$user->to('?path=overview/overview');
} else {
	// After return from Google
	if( !empty( $_GET['code'] ) ) {
		$client->authenticate( $_GET['code'] );


		if( $session->set( 'google', $client->getAccessToken() ) ) {
			$client->setAccessToken( $session->get( 'google' ) );

			$clientPlus = new Google_Service_Plus( $client );
			print_r($clientPlus);
			die();
			$user->to( '?path=overview/overview' );
		} else {
			echo $language->translate( 'Something went wrong logging in using Google' );
		}
	} else if( !empty( $_GET['error'] ) ) {
		echo $_GET['error'];
	} else {
		// Authenticate
		$user->to( $client->createAuthUrl() );
	}
}