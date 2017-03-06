<?php
if( !$user->isLoggedIn() && !$user->hasPermission($path) ) {
	$user->to('?path=users/login');
} else {
	if( empty( $id ) && !$db->exists( 'id', 'groups', 'id', $id ) ) {
		$user->to( '?path=plugins/overview' );
	} else {
		$form = new Form();
		$plugins = new Plugins();
		$data = $plugins->data($id)[0];
		$title = $language->translate( 'Edit' ).': '.$language->translate( $data['name'] );
		require_once $dash->getInclude( 'header' );

		if( $_POST ) {
			$validation = $form->check($_POST, array(
                'name' => array(
                    'required' => true,
                    'minLength' => 4,
                    'remember' => true,
                    'name' => 'Name'
                ),
                'icon' => array(
                    'required' => true,
                    'remember' => true,
                    'minLength' => 4,
                    'name' => 'Icon'
                ),
                'sort' => array(
                    'remember' => true,
                    'required' => true,
                    'numeric' => true,
                    'name' => 'Sort'
                )
			), [$language, 'translate'], $id);

			if( empty( $form->errors ) ) {
			    if( $plugins->edit($validation) ) {
			        $user->to('?path=plugins/overview&message='.$language->translate('Plugin edited').'&messageType=success');
                } else {
			        echo '<div class="alert sc-card sc-card-supporting">'.$language->translate('The plugin could not be updated').'</div>';
                }
            } else {
			    echo $form->outputErrors();
            }
		}
?>
		<form action="" method="post">
			<div class="sc-floating-input">
				<input type="text" name="name" id="name" required value="<?php echo ( !empty( $form->input('name') ) ? $form->input('name') : $data['name'] ); ?>">
				<label for="name"><?php echo $language->translate('Name'); ?> <em>(<?php echo $language->translate('in English'); ?>)</em></label>
			</div>

            <a href=""></a>

			<div class="sc-floating-input">
				<input type="text" name="icon" id="icon" required value="<?php echo ( !empty( $form->input('icon') ) ? $form->input('icon') : $data['icon'] ); ?>">
				<label for="icon"><?php echo $language->translate('Icon'); ?></label>
			</div>

            <div class="sc-floating-input">
                <input type="number" name="number" id="number" required value="<?php echo ( !empty( $form->input('sort') ) ? $form->input('sort') : $data['sort'] ); ?>">
                <label for="number"><?php echo $language->translate('Sort'); ?></label>
            </div>

			<button class="sc-raised-button"><i class="material-icons">save</i><?php echo $language->translate('Save'); ?></button>
		</form>
<?php
		require_once $dash->getInclude( 'footer' );
	}
}