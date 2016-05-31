<?php
// After return from Google
if( !empty( $_GET['code'] ) ) {

} else if( !empty( $_GET['error'] ) ) {
	echo $_GET['error'];
} else {
	// Authenticate
	$user->to($client->createAuthUrl());
}