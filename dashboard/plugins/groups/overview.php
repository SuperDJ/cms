<?php
if( !$user->isLoggedIn() && !$user->hasPermission($path) ) {
	$user->to('?path=users/login');
} else {
	$title = $language->translate( 'Overview' );
	require_once $dash->getInclude( 'header' );

	$group = new Group();
	$data = $group->data();

	echo '	<p class="sc-col sc-xs4 sc-s12">
				<a href="?path=groups/add" class="sc-raised-button">
					<i class="material-icons">add</i>'
					.$language->translate('Add group').'
				</a>
			</p>';
	if( empty( $data ) ) {
		echo $language->translate('No results found');
	} else {
		echo '	<table>
					<thead>
					<tr>
						<th>'.$language->translate('Group').'</th>
						<th>'.$language->translate('Description').'</th>
						<th>'.$language->translate('Rights').'</th>
						<th>'.$language->translate('Default group').'</th>
						<th>'.$language->translate('Options').'</th>
					</tr>
					</thead>
					
					<tbody>';

		// Check if user has permission
		($user->hasPermission('groups/edit') ? $edit = true : $edit = false);
		($user->hasPermission('groups/delete') ? $delete = true : $delete = false);
		foreach( $data as $row => $field ) {
			echo '	<tr>
						<td>'.$field['group'].'</td>
						<td>'.$field['description'].'</td>
						<td>'.$field['rights'].'</td>
						<td>'.($field['default'] == 1 ? '<i class="material-icons success">check</i>' : '<i class="material-icons error">clear</i>' ).'</td>
						<td>
						'.( $edit ? '
							<a href="?path=groups/edit&id='.base64_encode($field['id']).'" class="edit sc-flat-button">
								<i class="material-icons">edit</i>
							</a>' : '').'
						'.( $delete && !$db->exists('id', 'users', 'groups_id', $field['id']) ? '
							<a href="?path=groups/delete&id='.base64_encode($field['id']).'" class="delete sc-flat-button">
								<i class="material-icons">delete</i>
							</a>' : '').'	
						</td>
					</tr>';
		}

		echo '		</tbody>';
	}

	require_once $dash->getInclude( 'footer' );
}