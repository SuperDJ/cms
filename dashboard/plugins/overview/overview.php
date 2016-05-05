<?php
$title = $language->translate('Overview');
?>

<!DOCTYPE html>
<html class="no-js" lang="EN">
	<head>
		<title><?php echo $title ?></title>

		<meta charset="utf-8">
		<meta http-equiv="x-ua-compatible" content="ie=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="author" content="Derkjan Super">

		<link rel="stylesheet" href="/dashboard/style/css/admin.css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">
	</head>

	<body>
		<div data-sticky-container>
			<header class="top-bar" data-sticky data-options="marginTop:0;">
				<div class="top-bar-title">
					<span data-responsive-toggle="responsive-menu" data-hide-for="medium">
						<button class="menu-icon dark" type="button" data-toggle></button>
					</span>
					<?php echo $title; ?>
				</div>
				<div>
					<div class="top-bar-left"></div>
					<div class="top-bar-right">
						<form action="" method="post">
							<ul class="menu">
								<li><input type="search" placeholder="Search"></li>
								<li><button type="button" class="button"><i class="fa fa-search"></i></button></li>
							</ul>
						</form>
					</div>
				</div>
			</header>
		</div>

		<aside data-sticky-container>
			<nav data-sticky data-options="marginTop:0;">
				<ul class="vertical menu" data-accordion-menu>
					<li>
						<ul class="menu vertical nested">
							<li><a href="#">Item 1A</a></li>
							<li><a href="#">Item 1B</a></li>
						</ul>
					</li>
					<li><a href="#">Item 2</a></li>
				</ul>
			</nav>
		</aside>

		<main>

		</main>

		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
		<script type="text/javascript" src="/dashboard/style/bower_components/foundation-sites/dist/foundation.min.js"></script>
		<script type="text/javascript" src="/dashboard/style/js/app.js"></script>
	</body>
</html>