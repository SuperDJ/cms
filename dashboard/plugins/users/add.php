<?php
if( !$user->isLoggedIn() && !$user->hasPermission($path) ) {
	$user->to('?path=users/login');
} else {
	$title = $language->translate( 'Add' );
	require_once $dash->getInclude( 'header' );
	$form = new Form();

	if( $_POST ) {
		$validation = $form->check($_POST, array(
			'first_name'         => array(
				'required'  => true,
				'minLength' => 3,
				'remember'  => true,
				'name'      => 'First name'
			),
			'last_name'          => array(
				'required'  => true,
				'minLength' => 4,
				'remember'  => true,
				'name'      => 'Last name'
			),
			'email'              => array(
				'unique'   => 'users',
				'required' => true,
				'email'    => true,
				'remember' => true,
				'name'     => 'Email'
			),
			'group' => array(
				'required' => true,
				'numeric' => true,
				'name' => 'Group'
			)
		), [$language, 'translate']);

		if( empty( $form->errors ) ) {
			if( $user->add($validation) ) {
				$user->to('?path=users/overview&message='.$language->translate('User added').'&messageType=success');
			} else {
				echo '<div class="error sc-card sc-card-primary" role="alert">'.$language->translate('User could not be added').'</div>';
			}
		} else {
			echo $form->outputErrors();
		}
	}

?>
	<form action="" method="post">
		<div class="sc-floating-input">
			<input type="text" name="first_name" id="first_name" required value="<?php echo $form->input('first_name'); ?>">
			<label for="first_name"><?php echo $language->translate('First name'); ?></label>
		</div>

		<div class="sc-floating-input">
			<input type="text" name="last_name" id="last_name" required value="<?php echo $form->input('last_name'); ?>">
			<label for="last_name"><?php echo $language->translate('Last name'); ?></label>
		</div>

		<div class="sc-floating-input">
			<input type="email" name="email" id="email" required value="<?php echo $form->input('email'); ?>">
			<label for="email"><?php echo $language->translate('Email'); ?></label>
		</div>

		<select name="group" id="group" class="sc-select">
			<?php
			$group = new Group();
			foreach($group->data() as $row => $field ) {
				echo '<option value="'.$field['id'].'">'.$language->translate($field['group']).'</option>';
			}
			?>
		</select>
	</form>
<?php
	require_once $dash->getInclude('footer');
}