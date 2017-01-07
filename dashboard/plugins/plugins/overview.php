<?php
if( !$user->isLoggedIn() ) {
	$user->to('?path=users/login');
} else {
	print_r($_GET);
}