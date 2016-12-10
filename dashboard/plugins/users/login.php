<?php
if( $user->isLoggedIn() ) {
	$user->to('?path=overview/overview');
} else {
	// Define page title
	$title = $language->translate('Login');
	require_once $plugins->getHeader('lr');

	$form = new Form();
	// Check form
	if( $_POST ) {
		$validation = $form->check( $_POST, array(
			'email'              => array(
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
			if( $user->login( $validation ) ) {
				if( $session->set('user', $db->detail('id', 'users', 'email', $validation['email'])) ) {
					$user->to( '?path=overview/overview' );
				} else {
					echo '<div class="alert sc-card-supporting sc-card-supporting-additional">'.$language->translate('Something went wrong logging you in').'</div>';
				}
			} else {
				echo '<div class="alert sc-card-supporting sc-card-supporting-additional">'.$language->translate('Something went wrong logging you in').'</div>';
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
				<input type="email" name="email" id="email" value="<?php echo $form->input('email'); ?>" required>
				<label for="email"><?php echo $language->translate('Email'); ?></label>
			</div>

			<div class="sc-floating-input">
				<input type="password" name="password" id="password" required>
				<label for="password"><?php echo $language->translate('Password'); ?></label>
			</div>

			<div class="sc-col sc-xs4 sc-s12">
				<div class="sc-switch">
					<label>
						<?php echo $language->translate('Keep me logged in'); ?>
						<input type="checkbox">
						<span class="sc-lever"></span>
					</label>
				</div>
			</div>

			<div class="sc-col sc-xs4 sc-s12">
				<input type="text" name="captcha" class="captcha">
				<input type="password" name="password_encrypted" id="password_encrypted" class="captcha">
			</div>
		</div>

		<div class="sc-card-actions">
			<div class="sc-col sc-xs4 sc-s12">
				<button type="submit" class="sc-raised-button"><?php echo $language->translate('Login' ); ?></button>

				<!--<a href="?path=users/facebook-login" class="button facebook"><i	class="fa fa-facebook"></i> <?php echo $language->translate( 'Facebook login' ); ?></a>
				<a href="?path=users/google-login" class="button google"><i	class="fa fa-google"></i> <?php echo $language->translate( 'Google login' ); ?></a>-->
				<a href="?path=users/register" class="sc-flat-button"><?php echo $language->translate( 'Register' ); ?></a>
			</div>

			<div class="sc-col sc-xs4 sc-s12">
				<a href="?path=users/recover-request"><?php echo $language->translate('Forgot password?'); ?></a>
			</div>
		</div>
	</form>

	<?php
	require_once $plugins->getFooter( 'lr' );
}