<?php
if( !$user->isLoggedIn() && !$user->hasPermission($path) ) {
	$user->to('?path=overview/overview');
} else {
	if( $google->logout() ) {
		$user->to('?path=overview/overview&message='.$language->translate('Google logged out').'&messageType=success');
	}
}