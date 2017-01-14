<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/dashboard/core/engine.php';

// Check if path has been set
if( !empty( $_GET['path'] ) ) {
 	$path = $db->sanitize( $_GET['path'] );
	$plugins = new Plugins();
	$dash = new Dashboard( $path, [ $language, 'translate' ] );

	if( !empty( $_GET['id'] ) ) {
		$id = (int)$db->sanitize($_GET['id']);
	}

	// Check if path exists
	if( $dash->checkPath($path) ) {
		// If user has been remembered login
		/*if( $cooke->exists('user') ) {
			if( $user->cookieLogin() ) {
				// Return user to requested path
				$user->to($path);
			}
		}*/

		if( $user->isLoggedIn() ) {
			// TODO Add Group
		}

		require_once 'plugins/'.$dash->path.'.php';
	} else {
		echo $language->translate('The path').': <i><b>'.$dash->path.'</b></i> '.$language->translate('does not exists');
	}
} else {
	$user->to('?path=users/login');
}