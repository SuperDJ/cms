<?php
if( !$user->isLoggedIn() && !$user->hasPermission($path) ) {
	$user->to('?path=users/login');
} else {
	if( empty( $id ) && !$db->exists( 'id', 'groups', 'id', $id ) ) {
		$user->to( '?path=plugins/overview' );
	} else {
		$form = new Form();
		$plugins = new Plugins();
		$title = $language->translate( 'Edit' ).': '.$language->translate( $data[0]['group'] );
		require_once $dash->getInclude( 'header' );
		$data = $plugins->data($id);

		if( $_POST ) {
			$validation = $form->check($_POST, array(
				
			));
		}
?>
		<form action="" method="post">
			<div class="sc-floating-input">
				<input type="text" name="name" id="name" required value="<?php echo ( !empty( $form->input('name') ) ? $form->input('name') : $data['name'] ); ?>">
				<label for="name"><?php echo $language->translate('Name'); ?></label>
			</div>

			<div class="sc-floating-input">
				<input type="text" name="icon" id="icon" required value="<?php echo ( !empty( $form->input('icon') ) ? $form->input('icon') : $data['icon'] ); ?>">
				<label for="icon"><?php echo $language->translate('Icon'); ?></label>
			</div>

			<button class="sc-raised-button"><i class="material-icons">save</i><?php echo $language->translate('Save'); ?></button>
		</form>
<?php
		require_once $dash->getInclude( 'footer' );
	}
}