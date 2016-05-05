<?php
session_start(); // Start session

// Set charset
header( 'Content-Type: text/html; charset=UTF-8' );

// Set mb charset
mb_internal_encoding( 'UTF-8' );

// Set time zone
$date = new DateTime( null, new DateTimeZone( 'Europe/Amsterdam' ) );

// Set global variables
define( 'ROOT', $_SERVER['DOCUMENT_ROOT'] );
define( 'TEST', true );

// Global functions
require_once ROOT.'/dashboard/core/functions.php';

if( TEST === true ) {
	// Force PHP to show errors
	error_reporting( E_ALL );
	ini_set( 'display_errors', '1' );
} else {
	// Turn off all errors
	error_reporting(0);
}

// Individually load classes
spl_autoload_register(function( $class ) {
	require_once ROOT.'/dashboard/classes/'.$class.'.php';
});

// Load Facebook
require_once ROOT.'/vendor/autoload.php';

$db = new Database();
$user = new User();
$session = new Session();
$cookie = new Cookie();

// If the $_GET[] is set and not empty
if( !empty( $_GET['language'] ) ) {
	$lang = $db->sanitize($_GET['language']);

	// Check if language exists
	if( in_array( $lang, $language->languages ) ) {
		$session->set('language', $lang); // Put language in session
		$cookie->set('language', $lang, 31536000); // Set language cookie for 1 year
		$user->to($_SERVER['REQUEST_URI']); // Refresh page
	} else { // If the language doesn't exists
		$session->set('language', 'English'); // Set default language
		$user->to($_SERVER['REQUEST_URI']); // Refresh page
	}
} else { // If the $_GET[] isn't set or is empty
	// If a user is logged in set the desired language
	if( $user->isLoggedIn() ) {
		$session->set('language', $user->data['language']);
	}

	// If session doesn't exists
	if( !$session->exists('language') ) {
		// If cookie exists
		if( $cookie->exists('language') ) {
			$session->set('language', $cookie->get('language')); // Set cookie in session
			$user->to($_SERVER['REQUEST_URI']); // Refresh page
		} else { // If cookie doesn't exists
			$session->set('language', 'English'); // Set default language
			$user->to($_SERVER['REQUEST_URI']); // Refresh page
		}
	} else { // If session exists
		$cookie->set('language', $session->get('language'), 31536000); // Set cookie for 1 year
		$language = new Language( $session->get('language') );
	}
}