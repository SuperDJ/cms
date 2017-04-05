<?php
if( !$user->isLoggedIn() && !$user->hasPermission($path) ) {
	$user->to('?path=overview/overview');
} else {
	if( empty( $_GET['code'] ) ) {
		$user->to($google->getAuthUrl());
	} else {
		if( $google->checkRedirectCode() ) {
		 	//$user->to('?path=overview/overview&message='.$language->translate('Google logged in').'&messageType=success');
		}
	}
}