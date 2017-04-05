<?php
if( $user->isLoggedIn() ) {
	$user->to('?path=overview/overview');
} else {
	// Define page title
	$title = $language->translate('Recover request');
	require_once $plugins->getHeader('lr');

	$form = new Form($db);
	// Check form
	if( $_POST ) {
		$validation = $form->check( $_POST, array(
			'email'              => array(
				'exists'   => 'users',
				'required' => true,
				'email'    => true,
				'remember' => true,
				'name'     => $language->translate('Email')
			),
			'captcha'            => array(
				'captcha' => true,
				'name'    => 'Captcha'
			)
		), [ $language, 'translate' ] );
		// If there are no errors register user else show errors
		if( empty( $form->errors ) ) {
			if( $user->recover( $validation, [$language, 'translate'] ) ) {
				echo '<div class="success sc-card-supporting sc-card-supporting-additional">'.$language->translate('An email has been send to:').' '.$validation['email'].'</div>';
			} else {
				echo '<div class="error sc-card-supporting sc-card-supporting-additional">'.$language->translate('Something went wrong emailing you').'</div>';
			}
		} else {
			// Show errors
			echo $form->outputErrors();
		}
	}
	?>

	<form action="" method="post" autocomplete="off">
		<div class="sc-card-supporting sc-card-supporting-additional">
			<div class="sc-floating-input">
				<input type="email" name="email" id="email" required value="<?php echo $form->input('email'); ?>">
				<label for="email"><?php echo $language->translate('Email'); ?></label>
			</div>

			<div class="sc-col sc-xs4 sc-s12">
				<input type="text" name="captcha" class="captcha">
			</div>
		</div>

		<div class="sc-card-actions">
			<button class="sc-raised-button" type="submit"><?php echo $language->translate('Recover'); ?></button>
			<a href="?path=users/login" class="sc-flat-button"><?php echo $language->translate('Login'); ?></a>
		</div>
	</form>

	<?php
	require_once $plugins->getFooter( 'lr' );
}