<?php
if( !$user->isLoggedIn() && !$user->hasPermission($path) ) {
	$user->to('?path=users/login');
} else {
	$title = $language->translate( 'Add' );
	require_once $dash->getInclude( 'header' );
	$form = new Form($db);
	$email = new Email($db);

	if( $_POST ) {
		$validation = $form->check($_POST, array(
			'subject' => array(
				'required' => true,
				'remember' => true,
				'minLength' => 4,
				'name' => 'Subject'
			),
			'content' => array(
				'required' => true,
				'remember' => true,
				'name' => 'Content'
			),
			'language' => array(
				'required' => true,
				'numeric' => true,
				'name' => 'Language'
			),
			'to' => array(
				'numeric' => true,
				'name' => 'To'
			)
		), [$language, 'translate']);

		if( empty( $form->errors ) ) {
			if( $email->add($validation) ) {
				$user->to('?path=emails/overview&message='.$language->translate('Email has been send').'&messageType=success');
			} else {
				echo '<div class="error sc-card sc-card-supporting">'.$language->translate('Email has not been send').'</div>';
			}
		} else {
			echo $form->outputErrors();
		}
	}
?>
	<form action="" method="post">
		<div class="sc-floating-input">
			<input type="text" name="subject" id="subject" required value="<?php echo ( !empty( $form->input('subject') ) ? $form->input('subject') : '' ); ?>">
			<label for="subject"><?php echo $language->translate('Subject'); ?></label>
		</div>

		<div class="sc-multi-input">
			<textarea name="content" id="content" required><?php echo ( !empty( $form->input('content') ) ? $form->input('content') : '' ); ?></textarea>
			<label for="content"><?php echo $language->translate('Content'); ?></label>
		</div>


        <select class="sc-select" name="language" id="language">
            <?php
            foreach( $language->data() as $row => $field ) {
                echo '<option value="'.$field['id'].'">'.$language->translate($field['language']).'</option>';
            }
            ?>
        </select>

        <div class="sc-col sc-xs4 sc-s12">
			<?php echo $language->translate('If everyone is chosen, email will be send to everyone based on the selected language'); ?>.

            <select class="sc-select" name="to" id="to">
                <?php
                echo '<option value="0">'.$language->translate('Everyone').'</option>';

                foreach( $user->data() as $row => $field ) {
                    echo '<option value="'.$field['id'].'">'.substr( $field['first_name'], 0, 1 ).'. '.$field['last_name'].'</option>';
                }
                ?>
            </select>
        </div>

		<button class="sc-raised-button"><i class="material-icons">send</i> <?php echo $language->translate('Send'); ?></button>
	</form>
<?php
	require_once $dash->getInclude('footer');
}