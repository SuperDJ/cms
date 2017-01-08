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

		echo '	<form action="" method="post">';
		foreach( $data as $field => $value ) {
			echo '	<div class="sc-floating-input">
						<input type="text" name="'.$value['id'].'">
						<label for="'.$value['id'].'">'.$value['translation'].'</label>
					</div>';
		}
		echo '	</form>
				<button class="sc-raised-button"><i class="material-icons">save</i>'.$language->translate('Save').'</button>';

		require_once $dash->getInclude( 'footer' );
	}
}