<?php
if( !$user->isLoggedIn() && !$user->hasPermission($path) ) {
	$user->to('?path=overview/overview');
} else {
	if( $fb->logout() ) {
		$user->to('?path=overview/overview&message='.$language->translate('Facebook logged out').'&messageType=success');
	}
}