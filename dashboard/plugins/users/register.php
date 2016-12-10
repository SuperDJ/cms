<?php
if( $user->isLoggedIn() ) {
	$user->to('?path=overview/overview');
} else {
	$title = $language->translate('Register');

	require_once $plugins->getHeader('lr');

	$form = new Form();
	// Check form
	if( $_POST ) {
		$validation = $form->check( $_POST, array(
			'first_name'         => array(
				'required'  => true,
				'minLength' => 3,
				'remember'  => true,
				'name'      => $language->translate('First name')
			),
			'last_name'          => array(
				'required'  => true,
				'minLength' => 4,
				'remember'  => true,
				'name'      => $language->translate('Last name')
			),
			'email'              => array(
				'unique'   => 'users',
				'required' => true,
				'email'    => true,
				'remember' => true,
				'name'     => $language->translate('Email')
			),
			'password'           => array(
				'base64_decode' => true,
				'required'      => true,
				'minLength'     => 6,
				'name'          => $language->translate('Password')
			),
			'password_again'     => array(
				'base64_decode' => true,
				'required'      => true,
				'minLength'     => 6,
				'matches'       => 'password',
				'name'          => $language->translate('Password again')
			),
			'password_encrypted' => array(
				'required'  => true,
				'minLength' => 32,
				'maxLength' => 33,
				'name'      => $language->translate('Password encrypted')
			),
			'captcha'            => array(
				'captcha' => true,
				'name'    => 'Captcha'
			)
		), [ $language, 'translate' ] );

		// If there are no errors register user else show errors
		if( empty( $form->errors ) ) {
			if( $user->register( $validation ) ) {
				echo '<div class="success">'.$language->translate('You have been registered').'</div>';
			} else {
				echo '<div class="error">'.$language->translate('Something went wrong registering').'</div>';
			}
		} else {
			// Show errors
			echo $form->outputErrors();
		}
	}
	?>

	<form action="" method="post">
		<div class="sc-card-supporting sc-card-supporting-additional">
			<div class="sc-floating-input">
				<input type="text" name="first_name" id="first_name" required value="<?php echo $form->input('first_name'); ?>">
				<label for="first_name"><?php echo $language->translate('First name'); ?></label>
			</div>

			<div class="sc-floating-input">
				<input type="text" name="last_name" id="last_name" required value="<?php echo $form->input('last_name'); ?>">
				<label for="last_name"><?php echo $language->translate('Last name'); ?></label>
			</div>

			<div class="sc-floating-input">
				<input type="email" name="email" id="email" required value="<?php echo $form->input('email'); ?>">
				<label for="email"><?php echo $language->translate('Email'); ?></label>
			</div>

			<div class="sc-floating-input">
				<input type="password" name="password" id="password" required>
				<label for="password"><?php echo $language->translate('Password'); ?></label>
			</div>

			<div class="sc-floating-input">
				<input type="password" name="password_again" id="password_again" required>
				<label for="password_again"><?php echo $language->translate('Password again'); ?></label>
			</div>

			<div class="sc-col sc-xs4 sc-s12">
				<input type="text" name="captcha" class="captcha">
				<input type="password" name="password_encrypted" id="password_encrypted" class="captcha">
			</div>
		</div>

		<div class="sc-card-actions">
			<button class="sc-raised-button"><?php echo $language->translate('Register'); ?></button>
			<a href="?path=users/login" class="sc-flat-button"><?php echo $language->translate('Login'); ?></a>
		</div>
	</form>

	<?php
	require_once $plugins->getFooter( 'lr' );
}