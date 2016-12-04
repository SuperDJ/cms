<?php
if( !$user->isLoggedIn() ) {
	$user->to('?path=users/login');
} else {
	$title = $language->translate( 'Overview' );
?>
    <!DOCTYPE html>
    <html lang="EN">
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

                    <ul class="sc-appbar-menu-more" id="sc-nav-more">
                        <li>Example</li>
                    </ul>
                </div>
            </header>

            <aside id="sc-drawer" class="sc-drawer sc-drawer-persistent">
                <nav class="sc-drawer-container">
                    <ul>
                        <li class="sc-drawer-header">
                            <img src="/dashboard/stylesheets/images/profile.jpg" alt="Profile image" class="sc-drawer-profile-img">
                            <div class="sc-drawer-profile-name"><?php echo substr($user->data['first_name'], 0, 1).'. '.$user->data['last_name']; ?></div>
                            <div id="sc-drawer-profile-more" class="sc-drawer-profile-more">
                                <i class="material-icons sc-trigger" data-sc-trigger="sc-drawer-profile-more">arrow_drop_down</i>

                                <ul>
                                    <li><a href="#">Facebook</a></li>
                                    <li><a href="#">Google Plus</a></li>
                                    <li><a href="#">Add account</a></li>
                                </ul>
                            </div>
                        </li>
                        <li><a href="?path=overview/overview"><i class="material-icons">home</i> Home</a></li>
                        <?php
                        echo 'menu: ';
                        print_r($plugins->menu);
                        ?>
                    </ul>
                </nav>
            </aside>

            <main>

            </main>

            <script type="application/javascript" src="/dashboard/js/vendor.min.js"></script>
            <script type="application/javascript" src="/dashboard/js/app.min.js"></script>
        </body>
    </html>
<?php
}