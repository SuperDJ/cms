<?php
if( !$user->isLoggedIn() && !$user->hasPermission($path) ) {
	$user->to('?path=users/login');
} else {
    if( empty( $id ) && !$db->exists('id', 'languages', 'id', $id) ) {
        $user->to('?path=users/overview');
    }

	$data = $language->data($id)[0];
	$title = $language->translate( 'Edit' ).': '.$data['language'];
	require_once $dash->getInclude( 'header' );

	$form = new Form();

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
		), [$language, 'translate'], $id);

		if( empty( $validation->errors ) ) {
			if( $language->edit($validation) ) {
				$user->to('?path=languages/overview&message='.$language->translate('Language edited').'&messageType=success');
			} else {
				echo '<div class="alert sc-card sc-card-supporting">'.$language->translate('Something went wrong editing the language').': '.$language->translate($data['language']).'</div>';
			}
		} else {
			echo $form->outputErrors();
		}
	}
	?>
	<form action="" method="post">
		<div class="sc-floating-input">
			<input type="text" name="language" id="language" value="<?php echo ( !empty( $form->input('language') ) ? $form->input('language') : $data['language'] ); ?>" required>
			<label for="language"><?php echo $language->translate('Language'); ?> <em>(<?php echo $language->translate('in English'); ?>)</em></label>
		</div>

		<div class="sc-floating-input">
			<input type="text" name="iso_code" id="iso_code" value="<?php echo ( !empty( $form->input('iso_code') ) ? $form->input('iso_code') : $data['iso_code'] ); ?>" required>
			<label for="iso_code"><?php echo $language->translate('ISO code'); ?> <em>(EN, DE)</em></label>
		</div>

		<div class="sc-col sc-xs4 sc-s12">
			<button type="submit" class="sc-raised-button"><i class="material-icons">save</i> <?php echo $language->translate('Save'); ?></button>
		</div>
	</form>
	<?php
	require_once $dash->getInclude( 'footer' );
}