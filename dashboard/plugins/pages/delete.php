<?php
if( !$user->isLoggedIn() && !$user->hasPermission($path) ) {
	$user->to( '?path=users/login' );
} else {
	if( !empty( $_GET['id'] ) ) {
		$page = new Page($db);
		$id = $db->sanitize(base64_decode( $_GET['id'] ));

		if( $language->delete($id) ) {
			$user->to('?path=pages/overview&message='.$language->translate('Page has been deleted').'&messageType=success');
		} else {
			$user->to('?path=pages/overview&message='.$language->translate('Page could not be deleted').'&messageType=error');
		}
	} else {
		$user->to('?path=pages/overview');
	}
}