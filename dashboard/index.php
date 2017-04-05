<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/dashboard/core/engine.php';

// Check if path has been set
if( !empty( $_GET['path'] ) ) {
 	$path = $db->sanitize( $_GET['path'] );

 	// Make sure Facebook works
	if( strpos($path, 'facebook-login') !== false ) {
		echo 3;
		$path = rtrim($path, '/');
	}

	$plugins = new Plugins( $db );
	$dash = new Dashboard( $db, $path, [ $language, 'translate' ] );

	if( !empty( $_GET['id'] ) ) {
		$id = (int)$db->sanitize( base64_decode( $_GET['id'] ) );
	}

	// Check if path exists
	if( $dash->checkPath( $path ) ) {
		// If user has been remembered login
		/*if( $cooke->exists('user') ) {
			if( $user->cookieLogin() ) {
				// Return user to requested path
				$user->to($path);
			}
		}*/

		// If user has no permission for certain page redirect back
		if( $user->isLoggedIn() ) {
			if( !$user->hasPermission( $path ) ) {
				$user->to('?path=overview/overview');
			}

			//TODO Add AFK check (Optional)
		}

		require_once 'plugins/'.$dash->path.'.php';
	} else {
		echo $language->translate('The path').': <i><b>'.$dash->path.'</b></i> '.$language->translate('does not exists');
	}
} else {
	$user->to('?path=users/login');
}