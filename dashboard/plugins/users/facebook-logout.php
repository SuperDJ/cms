<?php
if( !$user->isLoggedIn() && !$user->hasPermission($path) ) {
	$user->to('?path=overview/overview');
} else {
	$user->to($fb->logout('https://cms.dsuper.nl/dashboard/'));
}