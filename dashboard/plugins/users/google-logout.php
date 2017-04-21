<?php
if( !$user->isLoggedIn() && !$user->hasPermission($path) ) {
	$user->to('?path=overview/overview');
} else {
	if( $session->delete('google') ) {
		$user->to('?path=overview/overview&message='.$language->translate('Google logged out').'&messageType=success');
	}
}