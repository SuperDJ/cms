<?php
if( !$user->isLoggedIn() && !$user->hasPermission($path) ) {
	$user->to('?path=users/login');
} else {
	$title = $language->translate( 'Add' );
	require_once $dash->getInclude( 'header' );

	$form = new Form($db);
	$media = new Media($db);

    if( !empty( $_FILES ) ) {
        $validation = $form->media($_FILES, [$language, 'translate']);

        if( $validation && empty( $form->errors ) ) {
			if( $media->add($_FILES) ) {
				$user->to( '?path=media/overview&message='.$language->translate( 'Files uploaded' ).'&messageType=success' );
			} else {
				echo '<div class="error sc-card sc-card-supporting" role="alert">'.$language->translate( 'Something went wrong uploading files' ).'</div>';
			}
		} else {
            echo $form->outputErrors();
        }
    }
?>
	<form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="files[]" id="files" multiple required>

		<button class="sc-raised-button"><i class="material-icons">file_upload</i><?php echo $language->translate('Upload'); ?></button>
	</form>
<?php
	require_once $dash->getInclude( 'footer' );
}