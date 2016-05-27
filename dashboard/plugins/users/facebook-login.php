<?php
if( $user->isLoggedIn() ) {
	$user->to('?path=overview/overview');
} else {
	if( !empty( $_GET['code'] ) && !empty( $_GET['state'] ) ) {
		if( !$session->exists( 'facebook' ) ) {
			if( $fb->setAccessToken() ) {

				$request = $fb->getRequest( [ 'id', 'first_name', 'last_name', 'email', 'languages', 'picture' ] );
				if( $user->facebookLogin( $request ) ) {
					$fb->data = $request;
					$user->to( '?path=overview/overview' );
				} else {
					echo 'Logging in using facebook went wrong';
				}
			} else {
				echo 'Something went wrong setting token';
			}
		} else {
			$user->to( '?path=overview/overview' );
		}
	} else {
		$user->to( $fb->urlGenerate( 'http://www.cms.dsuper.nl/dashboard/?path=users/facebook-login', [ 'email', 'user_likes' ] ) );
	}
}