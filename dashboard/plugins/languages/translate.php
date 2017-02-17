<?php
if( !$user->isLoggedIn() && !$user->hasPermission($path) ) {
	$user->to('?path=users/login');
} else {
	if( empty( $id ) && !$db->exists('id', 'languages', 'id', $id) ) {
		$user->to('?path=languages/overview');
	} else {
		$title = $language->translate( 'Translate' ).': '.$language->translate($db->detail('language', 'languages', 'id', $id));
		require_once $dash->getInclude( 'header' );
		$form = new Form();

		$data = $language->translationData(1); // TODO Get default `languages_id` from database
		//print_r($data);

		if( $_POST ) {
			$i = 0;
			$post = array();
			foreach( $_POST as $field => $value ) {
				if( !empty( $value ) ) {
					$post[$field] = array(
						'minLength' => 2,
						'name'      => $data[$i]['translation']
					);
				}
				$i++;
			}

			$validation = $form->check($_POST, $post, [$language, 'translate'], $id);

			if( empty( $form->errors ) ) {
				if( $language->translation( $validation ) ) {
					$user->to('?path=languages/overview&message='.$language->translate('Language translated').'&messageType=success');
				} else {
					echo '<div class="error sc-card sc-card-supporting" role="error">'.$language->translate('Something went wrong adding translations').'</div>';
				}
			}
		}

		echo '	<a href="?path=languages/overview" class="sc-raised-button"><i class="material-icons">arrow_back</i> '.$language->translate('Back').'</a>
				<form action="" method="post">';

		// Get translations from database
		$translated = $language->translated($id);
		foreach( $data as $field => $value ) {
			$key = array_search_multi($value['id'], $translated);

			if( !empty( $key ) || $key === 0 ) {
				$translation = $translated[$key]['translation'];
			} else {
				$translation = '';
			}

			echo '	<div class="sc-floating-input">
						<input type="text" name="'.$value['id'].'" id="'.$value['translation'].'" value="'.( !empty( $form->input($value['id']) ) ? $form->input($value['id']) : ( !empty( $translation ) ? $translation : '' ) ).'">
						<label for="'.$value['translation'].'">'.$language->translate($value['translation']).'</label>
					</div>';
		}
		echo '		<button class="sc-raised-button"><i class="material-icons">save</i>'.$language->translate('Save').'</button>
				</form>';

		require_once $dash->getInclude( 'footer' );
	}
}