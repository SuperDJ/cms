<?php
if( !$user->isLoggedIn() && !$user->hasPermission($path) ) {
	$user->to('?path=users/login');
} else {

	if( !empty( $id ) && $db->exists('id', 'files', 'id', $id) ) {
		// Delete file from database and from server
		$media = new Media();
		if(  $media->delete($id) ) {
			$user->to('?path=media/overview&message='.$language->translate('Media has been deleted').'&messageType=success');
		} else {
			$user->to('?path=media/overview&message='.$language->translate('Media could not be deleted').'&messageType=error');
		}
	} else {
		$user->to('?path=media/overview');
	}
}