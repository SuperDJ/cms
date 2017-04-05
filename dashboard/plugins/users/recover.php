<?php
if( $user->isLoggedIn() ) {
	$user->to('?path=overview/overview');
} else {
	if( !empty( $_GET['code'] ) ) {
		$code = $db->sanitize( $_GET['code'] );
		$explode = explode('|', base64_decode($code));
		$active = $explode[0];
		$email = $explode[1];

		if( $db->exists('email', 'users', 'email', $email) ) {

			// Define page title
			$title = $language->translate( 'Recover' );
			require_once $plugins->getHeader( 'lr' );

			$form = new Form($db);
			// Check form
			if( $_POST ) {
				$validation = $form->check( $_POST, array(
					'email'              => array(
						'exists'   => 'users',
						'required' => true,
						'email'    => true,
						'remember' => true,
						'name'     => $language->translate( 'Email' )
					),
					'password'           => array(
						'base64_decode' => true,
						'required'      => true,
						'minLength'     => 6,
						'name'          => $language->translate( 'New password' )
					),
					'password_again'     => array(
						'base64_decode' => true,
						'required'      => true,
						'minLength'     => 6,
						'matches'       => 'password',
						'name'          => $language->translate( 'New password again' )
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
					if( $user->recover( $validation, [ $language, 'translate' ], $code ) ) {
						echo '<div class="success">'.$language->translate( 'Your password has been updated' ).'</div>';
					} else {
						echo '<div class="error">'.$language->translate( 'Something went wrong recovering your password' ).'</div>';
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
						<input type="email" name="email" id="email" required
							   value="<?php echo $form->input( 'email' ); ?>">
						<label for="email"><?php echo $language->translate( 'Email' ); ?></label>
					</div>

					<div class="sc-floating-input">
						<input type="password" name="password" id="password" required>
						<label for="password"><?php echo $language->translate( 'New password' ); ?></label>
					</div>

					<div class="sc-floating-input">
						<input type="password" name="password_again" id="password_again" required>
						<label for="password_again"><?php echo $language->translate( 'New password again' ); ?></label>
					</div>

					<div class="sc-col sc-xs4 sc-s12">
						<input type="text" name="captcha" class="captcha">
						<input type="password" name="password_encrypted" id="password_encrypted" class="captcha">
					</div>
				</div>

				<div class="sc-card-actions">
					<button class="sc-raised-button"
							type="submit"><?php echo $language->translate( 'Recover' ); ?></button>
					<a href="?path=users/login"
					   class="sc-flat-button"><?php echo $language->translate( 'Login' ); ?></a>
				</div>
			</form>

			<?php
			require_once $plugins->getFooter( 'lr' );
		} else {
			$user->to('//google.com');
		}
	} else {
		$user->to('?path=users/recover-request');
	}
}