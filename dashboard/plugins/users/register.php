<?php
if( $user->isLoggedIn() ) {
	$user->to('?path=overview/overview');
} else {
	$title = $language->translate( 'Register' );

	require_once $plugins->getHeader( 'lr' );

	$form = new Form();
	// Check form
	if( $_POST ) {
		$validation = $form->check( $_POST, array(
			'first_name'         => array(
				'required'  => true,
				'minLength' => 3,
				'remember'  => true,
				'name'      => $language->translate( 'First name' )
			),
			'last_name'          => array(
				'required'  => true,
				'minLength' => 4,
				'remember'  => true,
				'name'      => $language->translate( 'Last name' )
			),
			'email'              => array(
				'unique'   => 'users',
				'required' => true,
				'email'    => true,
				'remember' => true,
				'name'     => $language->translate( 'Email' )
			),
			'password'           => array(
				'base64_decode' => true,
				'required'      => true,
				'minLength'     => 6,
				'name'          => $language->translate( 'Password' )
			),
			'password_again'     => array(
				'base64_decode' => true,
				'required'      => true,
				'minLength'     => 6,
				'matches'       => 'password',
				'name'          => $language->translate( 'Password again' )
			),
			'password_encrypted' => array(
				'required'  => true,
				'minLength' => 32,
				'maxLength' => 33,
				'name'      => $language->translate( 'Password encrypted' )
			),
			'captcha'            => array(
				'captcha' => true,
				'name'    => 'Captcha'
			)
		), [ $language, 'translate' ] );

		// If there are no errors register user else show errors
		if( empty( $form->errors ) ) {
			if( $user->register( $validation ) ) {
				echo '<div class="callout success" data-closable>'.$language->translate( 'You have been registered' ).'</div>';
			} else {
				echo '<div class="callout alert" data-closable>'.$language->translate( 'Something went wrong registering' ).'</div>';
			}
		} else {
			// Show errors
			echo $form->outputErrors();
		}
	}
	?>

	<form action="" method="post" onsubmit="encrypt()">
		<div class="row">
			<label for="first_name"><?php echo $language->translate( 'First name' ); ?></label>
			<input type="text" name="first_name" id="first_name" required
				   value="<?php echo $form->input( 'first_name' ); ?>">
		</div>

		<div class="row">
			<label for="last_name"><?php echo $language->translate( 'Last name' ); ?></label>
			<input type="text" name="last_name" id="last_name" required
				   value="<?php echo $form->input( 'last_name' ); ?>">
		</div>

		<div class="row">
			<label for="email"><?php echo $language->translate( 'Email' ); ?></label>
			<input type="email" name="email" id="email" required value="<?php echo $form->input( 'email' ); ?>">
		</div>

		<div class="row">
			<label for="password"><?php echo $language->translate( 'Password' ); ?></label>
			<input type="password" name="password" id="password" required onkeyup="hash()">
		</div>

		<div class="row">
			<label for="password_again"><?php echo $language->translate( 'Password again' ); ?></label>
			<input type="password" name="password_again" id="password_again" required>
		</div>

		<div class="row">
			<input type="text" name="captcha" class="captcha">
			<input type="password" name="password_encrypted" id="password_encrypted" class="captcha">

			<button class="button"><?php echo $language->translate( 'Register' ); ?> <i class="fa fa-cloud-upload"></i>
			</button>
			<a href="?path=users/login" class="button"><?php echo $language->translate( 'Login' ); ?> <i
					class="fa fa-sign-in"></i></a>
		</div>
	</form>

	<?php
	require_once $plugins->getFooter( 'lr' );
}