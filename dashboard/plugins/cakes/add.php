<?php
if( !$user->isLoggedIn() && !$user->hasPermission($path) ) {
	$user->to('?path=users/login');
} else {
	$title = $language->translate( 'Add' );
	require_once $dash->getInclude( 'header' );
	$form = new Form();
	$cake = new Cake();

	if( $_POST ) {
		$validation = $form->check($_POST, array(
			'cake' => array(
				'required' => true,
				'remember' => true,
				'minLength' => 4,
				'name' => 'Cake'
			),
			'description' => array(
				'remember' => true,
				'name' => 'Description'
			)
		), [$language, 'translate']);

		if( empty( $form->errors ) ) {
			if( $cake->add($validation) ) {
				$user->to('?path=cakes/overview&message='.$language->translate('Cake has been added').'&messageType=success');
			} else {
				echo '<div class="error sc-card sc-card-supporting">'.$language->translate('Cake has not been added').'</div>';
			}
		} else {
			echo $form->outputErrors();
		}
	}
	?>

	<form action="" method="post">
		<div class="sc-floating-input">
			<input type="text" name="cake" id="cake" required value="<?php echo ( !empty( $form->input('cake') ) ? $form->input('cake') : '' ); ?>">
			<label for="cake"><?php echo $language->translate('Cake'); ?></label>
		</div>

		<div class="sc-multi-input">
			<textarea name="description" id="description" required><?php echo ( !empty( $form->input('description') ) ? $form->input('description') : '' ); ?></textarea>
			<label for="description"><?php echo $language->translate('Description'); ?></label>
		</div>

		<button class="sc-raised-button"><i class="material-icons">save</i> <?php echo $language->translate('Save'); ?></button>
	</form>
	<?php
	require_once $dash->getInclude('footer');
}