<?php
if( !$user->isLoggedIn() ) {
	$user->to('?path=users/login');
} else {
	$title = $language->translate( 'Add' );
	require_once $dash->getInclude( 'header' );

	$form = new Form();

	if( $_POST ) {
	}
?>
	<form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="files[]" id="files" multiple required class="sc-file-input" data-multiple-caption="{count} <?php echo 'files selected'; ?>">
        <label for="files"><?php echo $language->translate('Choose files'); ?></label>

		<button class="sc-raised-button"><i class="material-icons">file_upload</i><?php echo $language->translate('Upload'); ?></button>
	</form>
<?php
	require_once $dash->getInclude( 'footer' );
}