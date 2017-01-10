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

		$translate = $db->select( "SELECT `id`, `translation` FROM `translations`WHERE `languages_id` = ?", array( 1 ) ); // TODO Get default `languages_id` from database
		$form = new Form();

		if( $_POST ) {
			$i = 0;
			foreach($_POST as $field => $value ) {
				$validation = $form->check( $_POST, array(
					$translate[$i]['id'] => array(
						'minLength' => 2,
						'name' => $language->translate($translate[$i]['translation'])
					)
				), [ $language, 'translate' ] );
				$i++;
			}

			if( empty( $form->errors ) ) {
				$translations = count($validation);
				$translated = 0;
				foreach( $validation as $key => $value ) {
					// $key = `translations`.`id`
					// $value = `translations`.`translation`
					//Check to see if it's only needed to update
					if( !empty( $db->select("SELECT `id` FROM `translations` WHERE `translations_id` = ? AND `languages_id` = ?", array( $translate[$i]['id'], $id ))) ) {
						if( $db->update("UPDATE `translations` SET `translation` = ? WHERE `translations_id` = ? AND `languages_id` = ?", array($value, $translate[$i]['id'], $id))) {
							$translated++;
						}
					} else {
						// If translation is added to database $translated + 1
						if( $db->insert( "INSERT INTO `translations` (`translations_id`, `translation`, `languages_id`) VALUES (?, ?, ?)", array( $key, $value, $id ) ) ) {
							$translated++;
						}
					}
				}

				if( $translations === $translated ) {
					$user->to('?path=languages/overview&message='.$language->translate('Language translated').'&messageType=success');
				} else {
					echo '<div class="error">'.$language->translate('Something went wrong adding translations');
				}
			}
		}

		echo '	<a href="?path=languages/overview" class="sc-raised-button"><i class="material-icons">arrow_back</i> '.$language->translate('Back').'</a>
				<form action="" method="post">';
		$i = 0;
		foreach( $translate as $field => $value ) {
			$translated = $db->select("SELECT `translation` FROM `translations` WHERE `translations_id` = ? AND `languages_id` = ?", array( $translate[$i]['id'], $id ) );

			echo '	<div class="sc-floating-input">
						<input type="text" name="'.$translate[$i]['id'].'" id="'.$value['translation'].'" value="'.( !empty( $form->input($value['id']) ) ? $form->input($value['id']) : $translated['translation']).'">
						<label for="'.$value['translation'].'">'.$language->translate($value['translation']).'</label>
					</div>';
			$i++;
		}
		echo '		<button class="sc-raised-button"><i class="material-icons">save</i>'.$language->translate('Save').'</button>
				</form>';

		require_once $dash->getInclude( 'footer' );
	}
}