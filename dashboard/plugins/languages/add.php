<?php
if( !$user->isLoggedIn() && !$user->hasPermission($path) ) {
	$user->to('?path=users/login');
} else {
	$title = $language->translate( 'Add' );
	require_once $dash->getInclude( 'header' );

	$form = new Form($db);

	if( $_POST ) {
		$validation = $form->check( $_POST, array(
			'language' => array(
				'required' => true,
				'unique' => 'languages',
				'name' => 'Language'
			),
			'iso_code' => array(
				'required' => true,
				'unique' => 'languages',
				'maxLength' => 2,
				'name' =>  'ISO code'
			)
		), [$language, 'translate']);

		if( empty( $validation->errors ) ) {
			if( $language->add($validation) ) {
				$user->to('?path=languages/overview&message='.$language->translate('Language added').'&messageType=success');
			} else {
				echo '<div class="alert sc-card sc-card-supporting">'.$language->translate('Something went wrong adding the language').': '.$validation['language'].'</div>';
			}
		} else {
			echo $form->outputErrors();
		}
	}
?>
	<form action="" method="post">
		<div class="sc-floating-input">
			<input type="text" name="language" id="language" value="<?php echo $form->input('language'); ?>" required>
			<label for="language"><?php echo $language->translate('Language'); ?> <em>(<?php echo $language->translate('in English'); ?>)</em></label>
		</div>

		<div class="sc-floating-input">
			<input type="text" name="iso_code" id="iso_code" value="<?php echo $form->input('iso_code'); ?>" required>
			<label for="iso_code"><?php echo $language->translate('ISO code'); ?> <em>(EN, DE)</em></label>
		</div>

		<div class="sc-col sc-xs4 sc-s12">
			<button type="submit" class="sc-raised-button"><i class="material-icons">add</i> <?php echo $language->translate('Add'); ?></button>
		</div>
	</form>
<?php
	require_once $dash->getInclude( 'footer' );
}