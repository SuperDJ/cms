<?php
if( !$user->isLoggedIn() ) {
	$user->to('?path=users/login');
} else {
	if( !empty( $_GET['id'] ) ) {
		if( $db->delete("DELETE FROM `languages` WHERE `id` = ?", array( $id ) ) ) {
			$user->to('?path=languages/overview&message='.$language->translate('Language has been deleted').'&messageType=success');
		} else {
			$user->to('?path=languages/overview&message='.$language->translate('Language could not be deleted').'&messageType=error');
		}
	} else {
		$user->to('?path=languages/overview');
	}
}