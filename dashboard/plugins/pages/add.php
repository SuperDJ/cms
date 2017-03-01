<?php
if( !$user->isLoggedIn() && !$user->hasPermission($path) ) {
	$user->to('?path=users/login');
} else {
	$title = $language->translate( 'Add' );
	require_once $dash->getInclude( 'header' );
	$form = new Form();

?>
	<form action="" method="post">
		<div class="sc-floating-input">
			<input type="text" name="title" id="title" required value="<?php echo ( !empty( $form->input('title') ? $form->input('title') : '') ); ?>">
			<label for="title"><?php echo $language->translate('Title'); ?></label>
		</div>

        <div class="sc-col sc-xs4">
            <textarea name="content" id="content"></textarea>
        </div>

        <select name="language" id="language" class="sc-select">
            <?php
                foreach( $language->data() as $row => $field ) {
                    echo '<option value="'.$field['id'].'">'.$language->translate($field['language']).'</option>';
                }
            ?>
        </select>

        <div class="sc-multi-input">
            <textarea name="keywords" id="keywords">
                <?php echo ( !empty( $form->input('keywords') ) ? $form->input('keywords')  : '' ); ?>
            </textarea>

            <label for="keywords" class="sc-tooltip" title="<?php echo $language->translate('Words or small sentence for search engines'); ?>">
                <?php echo $language->translate('Keywords').
                            '<em>('.$language->translate('Separated by comma').')</em>'; ?>
            </label>
        </div>
	</form>
    <script src="/dashboard/js/ckeditor/ckeditor.js"></script>
    <script>
		CKEDITOR.replace( 'content', {
                customConfig: '/dashboard/js/ckeditor-config.js'
		    }
        );

    </script>
<?php

	require_once $dash->getInclude( 'footer' );
}
?>