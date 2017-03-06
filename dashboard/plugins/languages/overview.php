<?php
if( !$user->isLoggedIn() && !$user->hasPermission($path) ) {
	$user->to('?path=users/login');
} else {
	$title = $language->translate( 'Overview' );
	require_once $dash->getInclude( 'header' );

	$data = $language->data();

	echo '<p class="sc-col sc-xs4 sc-s12"><a href="?path=languages/add" class="sc-raised-button">'.$language->translate('Add language').'</a></p>';

	if( !empty( $data ) ) {
		echo '	<table class="sc-table-hover">
					<thead>
						<tr>
							<th>'.$language->translate( 'Language' ).'</th>
							<th>'.$language->translate( 'ISO code' ).'</th>
							<th>'.$language->translate('Translated').'</th>	
							<th>'.$language->translate( 'Options' ).'</th>
						</tr>
					</thead>
					
					<tbody>';

		// Check if use has permission
		($user->hasPermission('languages/translate') ? $translate = true : $translate = false);
		($user->hasPermission('languages/edit') ? $edit = true : $edit = false);
		($user->hasPermission('languages/delete') ? $delete = true : $delete = false);
		foreach( $data as $key => $field ) {
			echo '		<tr>
							<td>'.$language->translate( $field['language'] ).'</td>
							<td>'.$field['iso_code'].'</td>
							<td>'.$field['translated'].'</td>
							<td>
							'.( $translate ? '
								<a href="?path=languages/translate&id='.base64_encode($field['id']).'" class="sc-flat-button">
									<i class="material-icons">translate</i>
								</a>' : '').'
							'.( $edit ? '
								<a href="?path=languages/edit&id='.base64_encode($field['id']).'" class="edit sc-flat-button">
									<i class="material-icons">edit</i>
								</a>' : '').'
							'.( $delete && !$db->exists('id', 'users', 'languages_id', $field['id']) ? '
								<a href="?path=languages/delete&id='.base64_encode($field['id']).'" class="delete sc-flat-button">
									<i class="material-icons">delete</i>
								</a>' : '').'	
								
							</td>
						</tr>';
		}
		echo '		</tbody>
			</table>';
	} else {
		echo $language->translate('No results found');
	}

	require_once $dash->getInclude( 'footer' );
}