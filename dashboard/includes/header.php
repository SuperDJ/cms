<!DOCTYPE html>
<html lang="EN"><!-- TODO make language dynamic -->
	<head>
		<title><?php echo $title ?></title>

		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
		<link rel="stylesheet" href="/dashboard/stylesheets/admin.css">

		<meta name="theme-color" content="#00BCD4">
		<meta name="msapplication-navbutton-color" content="#00BCD4">
		<meta name="apple-mobile-web-app-status-bar-style" content="#00BCD4">
	</head>

	<body>
		<header class="sc-appbar">
			<div class="sc-appbar-nav">
				<a href="#"><i class="material-icons sc-trigger" data-sc-trigger="sc-drawer">menu</i></a>
			</div>

			<div class="sc-appbar-title">
				<h1><?php echo $title; ?></h1>
			</div>

			<div class="sc-appbar-actions">
				<a href="#" class="sc-ripple sc-search-trigger"><i class="material-icons">search</i></a>

				<div class="sc-search-input">
					<form action="/index.php" method="post">
						<div class="sc-single-input">
							<input type="search" name="search" id="search">
							<label for="search">Search</label>
						</div>
					</form>
				</div>
			</div>

			<div class="sc-appbar-menu">
				<a href="#" class="sc-nav-more sc-trigger" data-sc-trigger="sc-nav-more"><i class="material-icons">more_vert</i></a>

				<nav class="sc-menu" id="sc-nav-more">
					<a href="#">Example</a>
				</nav>
			</div>
		</header>


        <nav id="sc-drawer" class="sc-drawer sc-drawer-persistent">
            <header class="sc-drawer-header">
                <img src="/dashboard/stylesheets/images/profile.jpg" alt="Profile image" class="sc-drawer-profile-img">
                <div class="sc-drawer-profile-name"><?php echo substr( $user->data['first_name'], 0, 1 ).'. '.$user->data['last_name']; ?></div>
                <div id="sc-drawer-profile-more" class="sc-drawer-profile-more">
                    <i class="material-icons sc-trigger" data-sc-trigger="profile-more">arrow_drop_down</i>

                    <nav class="sc-menu" id="profile-more">
                        <a href="#">Facebook</a>
                        <a href="#">Google Plus</a>
                        <a href="#">Add account</a>
                    </nav>
                </div>
            </header>
            <?php
            echo $dash->menu;
            ?>
        </nav>

		<main>
<?php
if( !empty( $_GET['message'] ) ) {
    echo '    <div class="sc-dialog sc-expanded" id="notification">
                    <div class="sc-dialog-container">
                        <div class="sc-dialog-title">'.$language->translate('Notification').'</div>
                        <div class="sc-dialog-content '.($_GET['messageType'] == 'success' ? 'sc-teal-text' : 'sc-red-text').'">
                            '.$db->sanitize( $_GET['message'] ).'
                        </div>
                        <div class="sc-dialog-actions">
                            <button class="sc-flat-button sc-trigger" data-sc-trigger="notification">'.$language->translate('Close').'</button>
                        </div>
                    </div>    
                </div>';
}
?>