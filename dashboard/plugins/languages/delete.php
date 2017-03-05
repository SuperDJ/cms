<?php
if( !$user->isLoggedIn() && !$user->hasPermission($path) ) {
	$user->to('?path=users/login');
} else {
	if( !empty( $_GET['id'] ) ) {
		$id = $db->sanitize($_GET['id']);
		if( $language->delete($id) ) {
			$user->to('?path=languages/overview&message='.$language->translate('Language has been deleted').'&messageType=success');
		} else {
			$user->to('?path=languages/overview&message='.$language->translate('Language could not be deleted').'&messageType=error');
		}
	} else {
		$user->to('?path=languages/overview');
	}
}