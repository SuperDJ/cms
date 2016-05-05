<?php
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
			'name'     => $language->translate( 'Email' )
		),
		'password'           => array(
			'base64_decode' => true,
			'required'      => true,
			'minLength'     => 6,
			'name'          => $language->translate( 'Password' )
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
		if( $user->login($validation) ) {
			echo '<div class="callout success" data-closable>'.$language->translate('You have are logged in').'</div>';
			$user->to('?path=overview/overview');
		} else {
			echo '<div class="callout alert" data-closable>'.$language->translate('Something went wrong logging you in').'</div>';
		}
	} else {
		// Show errors
		echo $form->outputErrors();
	}
}
?>

<form action="" method="post" onsubmit="encrypt()">
	<div class="row">
		<label for="email"><?php echo $language->translate('Email'); ?></label>
		<input type="email" name="email" id="email" required value="<?php echo $form->input('email'); ?>">
	</div>

	<div class="row">
		<label for="password"><?php echo $language->translate('Password'); ?></label>
		<input type="password" name="password" id="password" required onkeyup="hash()">
	</div>

	<div class="row">
		<div class="columns small-12 medium-6"><?php echo $language->translate('Remember me'); ?></div>

		<div class="columns small-12 medium-6">
			<div class="switch small">
				<input class="switch-input" id="remember" type="checkbox" name="remember">
				<label class="switch-paddle" for="remember">
					<span class="show-for-sr"><?php echo $language->translate('Remember me'); ?></span>
					<span class="switch-active" aria-hidden="true">Yes</span>
					<span class="switch-inactive" aria-hidden="true">No</span>
				</label>
			</div>
		</div>
	</div>

	<div class="row">
		<input type="text" name="captcha" class="captcha">
		<input type="password" name="password_encrypted" id="password_encrypted" class="captcha">

		<button class="button"><?php echo $language->translate('Login'); ?> <i class="fa fa-sign-in"></i></button>
		<a href="?path=users/facebook-login" class="button facebook"><i class="fa fa-facebook"></i> <?php echo $language->translate('Facebook login'); ?></a>
		<a href="?path=users/google-login" class="button google"><i class="fa fa-google"></i> <?php echo $language->translate('Google login'); ?></a>
		<a href="?path=users/register" class="button"><?php echo $language->translate('Register'); ?> <i class="fa fa-cloud-upload"></i></a>
	</div>
</form>

<?php
require_once $plugins->getFooter('lr');