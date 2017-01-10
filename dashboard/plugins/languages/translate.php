<?php
if( !$user->isLoggedIn() ) {
	$user->to('?path=users/login');
} else {
	if( empty( $_GET['id'] ) ) {
		$user->to('?path=languages/overview');
	} else {
		$id = (int)$db->sanitize( $_GET['id'] );

		$title = $language->translate( 'Translate' ).' '.$language->translate($db->detail('language', 'languages', 'id', $id));
		require_once $dash->getInclude( 'header' );

		$data = $db->select( "SELECT `id`, `translation` FROM `translations`WHERE `languages_id` = ?", array( 1 ) ); // TODO make languages id dynamic
		$form = new Form();

		if( $_POST ) {
			$i = 0;
			foreach($_POST as $field => $value ) {
				$validation = $form->check( $_POST, array(
					$data[$i]['translation'] => array(
						'minLength' => 2,
						'name' => $language->translate($data[$i]['translation'])
					)
				), [ $language, 'translate' ] );
				$i++;
			}

			print_r($validation);
			die();

			if( empty( $form->errors ) ) {
				if( $db->insert() ) {}
			}
		}

		echo '	<form action="" method="post">';
		$i = 0;
		foreach( $data as $field => $value ) {
			echo '	<div class="sc-floating-input">
						<input type="text" name="'.$data[$i]['translation'].'" value="'.$form->input($value['id']).'">
						<label for="'.$value['id'].'">'.$language->translate($value['translation']).'</label>
					</div>';
			$i++;
		}
		echo '		<button class="sc-raised-button"><i class="material-icons">save</i>'.$language->translate('Save').'</button>
				</form>';

		require_once $dash->getInclude( 'footer' );
	}
}