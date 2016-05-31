<?php
class Google extends Google_Client {
	private $_scopes = array(
		'https://www.googleapis.com/auth/gmail.modify',
		'https://www.googleapis.com/auth/gmail.send',
		'https://www.googleapis.com/auth/contacts',
		'https://www.googleapis.com/auth/calendar',
		'https://www.googleapis.com/auth/plus.login',
		'https://www.googleapis.com/auth/plus.me');

	function __construct() {
		parent::__construct();
		// Get credentials
		$this->setAuthConfigFile( ROOT.'/google-credentials.json' );
		$this->addScope($this->_scopes);
		$this->setRedirectUri( 'http:/'.$_SERVER['HTTP_HOST'].'/dashboard/?path=users/google-login' );
	}
}