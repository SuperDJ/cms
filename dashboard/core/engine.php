<?php
session_start(); // Start session

// Set charset
header( 'Content-Type: text/html; charset=UTF-8' );

// Set mb charset
mb_internal_encoding( 'UTF-8' );

// Set time zone
$date = new DateTime( null, new DateTimeZone( 'Europe/Amsterdam' ) );

// Set global variables
$root = $_SERVER['DOCUMENT_ROOT'].'/dashboard/';
set_include_path( $root );
require_once 'core/defines.php';

// Global functions
require_once 'core/functions.php';

if( TEST === true ) {
	// Force PHP to show errors
	error_reporting( E_ALL );
	ini_set( 'display_errors', '1' );
} else {
	// Turn off all errors
	error_reporting(0);
}

// Auto loader for facebook
require_once 'sdk/Facebook/autoload.php';

// Auto loader for Google
require_once 'sdk/Google/vendor/autoload.php';

// Individually load classes
spl_autoload_register(function( $class ) {
	require_once 'classes/'.$class.'.php';
});

$db = new Database();
$user = new User( $db );
$session = new Session();
$cookie = new Cookie();
$fb = new Facebook();
$google = new Google();

// If a user is logged in set the desired language
if( $user->isLoggedIn() ) {
	$session->set('language', $user->data['languages_id']);
}

// If session doesn't exists
if( !$session->exists('language') ) {
	// If cookie exists
	if( $cookie->exists('language') ) {
		$session->set('language', $cookie->get('language')); // Set cookie in session
	} else { // If cookie doesn't exists
		$session->set('language', 1); // Set default language // TODO Make 1 (default) dynamic
	}
}

if( $session->exists('language') ) {
	if( $session->get('language') != $cookie->get('language') ) {
		$cookie->set( 'language', $session->get( 'language' ), 31536000 ); // Set cookie for 1 year // TODO Make length dynamic
		$language = new Language( $db, $session->get( 'language' ) );
	} else {
		$language = new Language( $db, $session->get( 'language' ) );
	}
}