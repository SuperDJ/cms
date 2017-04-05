<?php
if( !$user->isLoggedIn() && !$user->hasPermission($path) ) {
	$user->to('?path=users/login');
} else {
	if( !empty( $_GET['id'] ) ) {
		if( $user->delete( $id ) ) {
			$user->to('?path=users/overview&message='.$language->translate('User has been deleted').'&messageType=success');
		} else {
			$user->to('?path=users/overview&message='.$language->translate('User could not be deleted').'&messageType=error');
		}
	} else {
		$user->to('?path=users/overview');
	}
}