<?php
if( !$user->isLoggedIn() ) {
	$user->to('?path=users/login');
} else {
	if( $session->delete('user') ) {
		$user->to('?path=users/login');
	} else {
		$user->to('?path=overview/overview');
	}
}