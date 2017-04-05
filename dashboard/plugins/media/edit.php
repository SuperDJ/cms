<?php
if( !$user->isLoggedIn() && !$user->hasPermission($path) ) {
	$user->to('?path=users/login');
} else {
	if( empty( $id ) && !$db->exists('id', 'files', 'id', $id) ) {
		$user->to('?path=media/overview');
	} else {
		$title = $language->translate( 'Edit' );
		require_once $dash->getInclude( 'header' );

		$form = new Form($db);
		$media = new Media($db);

		if( $_POST ) {
		    $validation = $form->check($_POST, array(
		        'title' => array(
		            'required' => true,
                    'remember' => true,
                    'minLength' => 4,
                    'name' => 'Title'
                ),
                'description' => array(
		            'required' => true,
                    'remember' => true,
					'name' => 'Description'
                )
            ), [$language, 'translate'], $id);

		    if( empty( $form->errors ) ) {
		        if( $media->edit($validation) ) {
		            $user->to('?path=media/overview&message='.$language->transalte('Media edited').'&messageType=success');
                } else {
		            echo '<div class="error sc-card sc-card-supporting" role="alert">'.$language->translate('Something went wrong editing media').'</div>';
                }
            } else {
		        echo $form->outputErrors();
            }
        }

?>
		<form action="" method="post">
			<div class="sc-floating-input">
				<input type="text" name="title" id="title" required value="<?php echo ( !empty( $form->input('title') ) ? $form->input('title') : '' ); ?>">
				<label for="title"><?php echo $language->translate('Title'); ?></label>
			</div>

			<div class="sc-multi-input">
				<textarea name="description" id="description">
					<?php echo ( !empty( $form->input('description') ) ? $form->input('description') : ''); ?>
				</textarea>
				<label for="description"><?php echo $language->translate('Description'); ?></label>
			</div>

			<button class="sc-raised-button">
				<i class="material-icons">save</i>
				<?php echo $language->translate('Save'); ?>
			</button>
		</form>
<?php
		require_once $dash->getInclude( 'footer' );
	}
}