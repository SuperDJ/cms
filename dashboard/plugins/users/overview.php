<?php
if( !$user->isLoggedIn() && !$user->hasPermission($path) ) {
	$user->to('?path=users/login');
} else {
	$title = $language->translate('Overview');
	require_once $dash->getInclude('header');
	$data = $db->query("SELECT `u`.`id`, `first_name`, `last_name`, `email`, `register_date`, `active_date`, `group`, `active` FROM `users` `u`
						  JOIN `groups` `g`
							ON `g`.`id` = `u`.groups_id", array(), array('multipleRows'));

	echo '	<a href="?path=users/add" class="sc-raised-button">
				<i class="material-icons">add</i>
				'.$language->translate('Add user').
			'</a>';
	if( empty( $data ) ) {
		echo '<p class="sc-col sc-x4 sc-s12">'.$language->translate('No results found').'</p>';
	} else {
		echo '    <table class="sc-table-hover">
					<thead>
						<tr>
							<th>'.$language->translate( 'First name' ).'</th>
							<th>'.$language->translate( 'Last name' ).'</th>
							<th>'.$language->translate('Email').'</th>
							<th>'.$language->translate( 'Group' ).'</th>
							<th>'.$language->translate( 'Register date' ).'</th>
							<th>'.$language->translate( 'Last active' ).'</th>
							<th>'.$language->translate( 'Active' ).'</th>
							<th>'.$language->translate( 'Options' ).'</th>
						</tr>
					</thead>
					
					<tbody>';

		// Check if user has permission
		($user->hasPermission('users/edit') ? $edit = true : $edit = false);
		($user->hasPermission('users/delete') ? $delete = true : $delete = false);
		foreach( $data as $key => $field ) {
			echo '		<tr>
							<td>'.$field['first_name'].'</td>
							<td>'.$field['last_name'].'</td>
							<td>'.$field['email'].'</td>
							<td>'.$language->translate($field['group']).'</td>
							<td>'.$field['register_date'].'</td>
							<td>'.$field['active_date'].'</td>
							<td>'.( $field['active'] == 1 ? '<i class="material-icons success">check</i>' : '<i class="material-icons error">clear</i>' ).'</td>
							<td>
							'.( $edit ? '
								<a href="?path=users/edit&id='.base64_encode( $field['id'] ).'" class="edit sc-flat-button">
									<i class="material-icons">edit</i>
								</a>' : '').'
							'.( $delete ? '
								<a href="?path=users/delete&id='.base64_encode( $field['id'] ).'" class="delete sc-flat-button">
									<i class="material-icons">delete</i>
								</a>' : '').'
							</td>
						</tr>';
		}
		echo '      	</tbody>
            		</table>';
	}

	require_once $dash->getInclude('footer');
}