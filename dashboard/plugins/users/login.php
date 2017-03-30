<?php
if( $user->isLoggedIn() ) {
	$user->to('?path=overview/overview');
} else {
	// Define page title
	$title = $language->translate('Login');
	require_once $dash->getInclude('header', 'lr');

	echo '<div class="sc-card-supporting sc-card-supporting-additional">';
	$form = new Form();
	// Check form
	if( $_POST ) {
		$validation = $form->check( $_POST, array(
			'email'              => array(
				'required' => true,
				'email'    => true,
				'remember' => true,
				'name'     => 'Email'
			),
			'password'           => array(
				'base64_decode' => true,
				'required'      => true,
				'minLength'     => 6,
				'name'          => 'Password'
			),
			'password_encrypted' => array(
				'required'  => true,
				'minLength' => 32,
				'maxLength' => 33,
				'name'      => 'Password encrypted'
			),
            /*'remember' => array(
                'name' => $language->translate('Keep me logged in')
            ),*/
			'captcha'            => array(
				'captcha' => true,
				'name'    => 'Captcha'
			)
		), [ $language, 'translate' ] );

		// If there are no errors register user else show errors
		if( empty( $form->errors ) ) {
			if( $user->login( $validation ) ) {
			    $details = array(
			        'id' => $db->detail('id', 'users', 'email', $validation['email']),
                    'group' => $db->detail('groups_id', 'users', 'email', $validation['email'])
                );
				if( $session->set('user', $details) ) {
				    // If the user wants to be remembered
				    /*if( $validation['remember'] == 1 ) {
				        $cookie->set('user', $session->get('user'), 60*60*24*30); // TODO Make cookie time a setting/ dynamic
                    }            */
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

        <form action="" method="post" autocomplete="off">
            <div class="sc-floating-input">
                <input type="email" name="email" id="email" value="<?php echo $form->input('email'); ?>" required>
                <label for="email"><?php echo $language->translate('Email'); ?></label>
            </div>

            <div class="sc-floating-input">
                <input type="password" name="password" id="password" required>
                <label for="password"><?php echo $language->translate('Password'); ?></label>
            </div>

            <!--<div class="sc-col sc-xs4 sc-s12">
                <div class="sc-switch">
                    <label>
                        <?php /*echo $language->translate('Keep me logged in'); */?>
                        <input type="checkbox" name="remember" value="1">
                        <span class="sc-lever"></span>
                    </label>
                </div>
            </div>-->

            <div class="sc-col sc-xs4 sc-s12">
                <input type="text" name="captcha" class="captcha">
                <input type="password" name="password_encrypted" id="password_encrypted" class="captcha">
            </div>
        </div>

        <div class="sc-card-actions">
            <div class="sc-col sc-xs4 sc-s12">
                <div class="sc-row">
                    <button type="submit" class="sc-raised-button"><?php echo $language->translate('Login' ); ?></button>
                    <a href="?path=users/register" class="sc-flat-button"><?php echo $language->translate( 'Register' ); ?></a>
                </div>
            </div>

            <div class="sc-col sc-xs4 sc-s12">
                <a href="?path=users/recover-request"><?php echo $language->translate('Forgot password'); ?>?</a>
            </div>
        </form>
    </div>

	<?php
	require_once $dash->getInclude('footer', 'lr');
}