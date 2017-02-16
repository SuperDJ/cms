<?php
if( !$user->isLoggedIn() && $user->hasPermission($path) ) {
	$user->to('?path=users/login');
} else {
	if( empty( $_GET['id'] ) ) {
		$user->to('?path=languages/overview');
	} else {
		$title = $language->translate( 'Translate' ).': '.$language->translate($db->detail('language', 'languages', 'id', $id));
		require_once $dash->getInclude( 'header' );
		$form = new Form();

		$translate = $db->query( "SELECT `id`, `translation` FROM `translations`WHERE `languages_id` = ?", array( 1 ) ); // TODO Get default `languages_id` from database

		if( $_POST ) {
			$i = 0;
			foreach( $_POST as $field => $value ) {
				if( !empty( $value ) ) {
					$validation = $form->check( $_POST, array(
						$translate[$i]['id'] => array(
							'minLength' => 2,
							'name'      => $translate[$i]['translation']
						)
					), [ $language, 'translate' ] );
				}
				$i++;
			}

			if( empty( $form->errors ) ) {
				$translations = count($validation);
				$translated = 0;
				foreach( $validation as $field => $value ) {
					// $field = `translations`.`id`
					// $value = `translations`.`translation`
					//Check to see if it's only needed to update
					$isTranslated = $db->query("SELECT `translation` FROM `translations` WHERE `translations_id` = ? AND `languages_id` = ?", array( $field, $id ))['translation'];

					if( !empty( $isTranslated ) ) {
						if( $isTranslated == $value ) {
							$translated++;
						} else {
							if( $db->query( "UPDATE `translations` SET `translation` = ? WHERE `translations_id` = ? AND `languages_id` = ?", array( $value, $field, $id ) ) ) {
								$translated++;
							}
						}
					} else {
						// If translation is added to database $translated + 1
						if( $db->query( "INSERT INTO `translations` (`translations_id`, `translation`, `languages_id`) VALUES (?, ?, ?)", array( $field, $value, $id ) ) ) {
							$translated++;
						}
					}
				}

				if( $translations === $translated ) {
					$user->to('?path=languages/overview&message='.$language->translate('Language translated').'&messageType=success');
				} else {
					echo '<div class="error sc-card sc-card-supporting">'.$language->translate('Something went wrong adding translations').'</div>';
				}
			}
		}

		echo '	<a href="?path=languages/overview" class="sc-raised-button"><i class="material-icons">arrow_back</i> '.$language->translate('Back').'</a>
				<form action="" method="post">';

		foreach( $translate as $field => $value ) {
			$translated = $db->query("SELECT `translation` FROM `translations` WHERE `translations_id` = ? AND `languages_id` = ?", array( $value['id'], $id ) )['translation'];
			echo '	<div class="sc-floating-input">
						<input type="text" name="'.$value['id'].'" id="'.$value['translation'].'" value="'.( !empty( $form->input($value['id']) ) ? $form->input($value['id']) : $translated).'">
						<label for="'.$value['translation'].'">'.$language->translate($value['translation']).'</label>
					</div>';
		}
		echo '		<button class="sc-raised-button"><i class="material-icons">save</i>'.$language->translate('Save').'</button>
				</form>';

		require_once $dash->getInclude( 'footer' );
	}
}