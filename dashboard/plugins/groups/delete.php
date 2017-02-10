<?php
if( !$user->isLoggedIn() ) {
	$user->to('?path=users/login');
} else {
	if( !empty( $_GET['id'] ) ) {
		if( $db->query("DELETE FROM `groups` WHERE `id` = ?", array( $id ) ) ) {
			$user->to('?path=groups/overview&message='.$language->translate('Group has been deleted').'&messageType=success');
		} else {
			$user->to('?path=groups/overview&message='.$language->translate('Group could not be deleted').'&messageType=error');
		}
	} else {
		$user->to('?path=groups/overview');
	}
}