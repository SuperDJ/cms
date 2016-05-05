<?php
$fb = new Facebook();

if( empty( $session->get('facebook_token') ) ) {
	// After returning from Facebook
	if( !empty( $_GET['code'] ) && !empty( $_GET['state'] ) ) {
		if( !empty( $fb->accessToken() ) ) {
			if( $fb->setAccessToken() ) {

				$request = $fb->getRequest( [ 'id', 'first_name', 'last_name', 'email' ] );
				if( $user->facebookLogin( $request ) ) {
					$fb->data = $request;
					$user->to( '?path=overview/overview' );
				} else {
					echo 'Logging in using facebook went wrong';
				}
			} else {
				echo 'Something went wrong setting token';
			}
		}
	} else {
		$user->to( $fb->urlGenerate( 'http://www.cms.dsuper.nl/dashboard/?path=users/facebook-login', ['email'] ) );
	}
} else {
	echo $session->get('facebook_token');
	die();
	if( $session->delete('facebook_token') ) {
		$user->to('?path=users/facebook-login');
	}
}