<?php
if( !$user->isLoggedIn() && !$user->hasPermission($path) ) {
	$user->to('?path=users/login');
} else {
	if( !empty( $_GET['id'] ) ) {
		$id = (int)$db->sanitize( base64_decode( $_GET['id'] ) );
		if( $db->exists('id', 'emails', 'id', $id) ) {

			$email = new Email($db);
			if( $email->delete( $id ) ) {
				$user->to('?path=emails/overview&message='.$language->translate('Email has been deleted').'&messageType=success');
			} else {
				$user->to('?path=emails/overview&message='.$language->translate('Email could not be deleted').'&messageType=error');
			}
		} else {
			$user->to('?path=emails/overview');
		}
	} else {
		$user->to('?path=emails/overview');
	}
}