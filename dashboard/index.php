<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/dashboard/core/engine.php';

// Check if path has been set
if( !empty( $_GET['path'] ) ) {
 	$path = $db->sanitize( $_GET['path'] );
	$plugins = new Plugins($path);

	// Check if path exists
	if( $plugins->check($plugins->path) ) {
		if( $user->isLoggedIn() ) {
			// TODO Add Group
		}

		require_once 'plugins/'.$plugins->path.'.php';
	} else {
		echo $language->translate('The path').': <i><b>'.$plugins->path.'</b></i> '.$language->translate("does not exists");
	}
} else {
	$user->to('?path=users/login');
}