<?php
if( !$user->isLoggedIn() ) {
	echo 2;
	die();
} else {
	echo 3;
	die();
	//$title = $language->translate( 'Overview' );
?><!--
	<!DOCTYPE html>
	<html lang="EN"><!-- TODO make language dynamic -->
		<!--<head>
			<title><?php /*echo $title */?></title>

			<meta charset="utf-8">
			<meta http-equiv="x-ua-compatible" content="ie=edge">
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
		</head>

		<body>
		</body>
	</html>
--><?php
}