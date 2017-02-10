<?php
if( !$user->isLoggedIn() ) {
	$user->to('?path=overview/overview');
} else {
	// Define page title
	$title = $language->translate( 'Edit' ).': '.substr($db->detail('first_name', 'users', 'id', $id), 0, 1).'. '.$db->detail('last_name', 'users', 'id', $id);
	require_once $dash->getInclude( 'header' );

	$data = $db->query("SELECT `active`, `groups_id`, `group` FROM `users` `u` JOIN `groups` `g` ON `g`.`id` = `u`.`groups_id` WHERE `u`.`id` = ?", array($id));
	$groups = $db->query("SELECT `id`, `group` FROM `groups`", array(), array('multipleRows'));

	$form = new Form();
	// Check form
	if( $_POST ) {
		$validation = $form->check( $_POST, array(), [ $language, 'translate' ], $id );
	}

?>
	<form action="" method="post">
		<div class="sc-col sc-xs">
			<label for="active"><?php echo $language->translate('Active'); ?></label>
			<div class="sc-switch">
				<label>
					<?php echo $language->translate('Inactive'); ?>
					<input type="checkbox" name="active" id="active" <?php echo ( $data[0]['active'] == 1 ? 'checked' : '' ); ?>>
					<span class="sc-lever"></span>
					<?php echo $language->translate('Active'); ?>
				</label>
			</div>
		</div>

        <div class="sc-col sc-xs">
            <label for="group"><?php echo $language->translate('Group'); ?></label>
            <select name="group" id="group" class="sc-select">
                <?php
                echo '<option value="'.$data[0]['groups_id'].'">'.$language->translate($data[0]['group']).'</option>';

                foreach($groups as $group => $field ) {
                    if( $field['id'] != $data[0]['groups_id'] ) {
                        echo '<option value="'.$field['id'].'">'.$language->translate($field['group']).'</option>';
                    }
                }
                ?>
            </select>
        </div>
	</form>
<?php
	require_once $dash->getInclude('footer');
}