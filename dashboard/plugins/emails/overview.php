<?php
if( !$user->isLoggedIn() && !$user->hasPermission($path) ) {
	$user->to('?path=users/login');
} else {
	$title = $language->translate( 'Overview' );
	require_once $dash->getInclude( 'header' );

	$email = new Email();
	$data = $email->data();

	echo '	<p class="sc-col sc-xs4 sc-s12">
				<a href="?path=emails/add" class="sc-raised-button">
					<i class="material-icons">add</i>'
		.$language->translate('Send email').'
				</a>
			</p>';
	if( empty( $data ) ) {
		echo $language->translate('No results found');
	} else {
		echo '	<table>
					<thead>
					<tr>
						<th>'.$language->translate('Subject').'</th>
						<th>'.$language->translate('Read').'</th>
						<th>'.$language->translate('Send by').'</th>
						<th>'.$language->translate('Send to').'</th>
						<th>'.$language->translate('Options').'</th>
					</tr>
					</thead>
					
					<tbody>';

		// Check if user has permission
		($user->hasPermission('emails/delete') ? $delete = true : $delete = false);
		foreach( $data as $row => $field ) {
			echo '	<tr>
						<td>'.$field['subject'].'</td>
						<td>'.( $field['read'] == 1 ? '<i class="material-icons success">check</i>' : '<i class="material-icons error">clear</i>' ).'</td>
						<td>'.$field['email'].'</td>
						<td>'.($field['to'] == 0 ? $language->translate('Everyone') : $db->detail('email', 'users', 'id', $field['to']) ).'</td>
						<td>
						'.( $delete && !$db->exists('id', 'users', 'groups_id', $field['id']) ? '
							<a href="?path=emails/delete&id='.base64_encode($field['id']).'" class="delete sc-flat-button">
								<i class="material-icons">delete</i>
							</a>' : '').'	
						</td>
					</tr>';
		}

		echo '		</tbody>
				</table>';
	}

	require_once $dash->getInclude( 'footer' );
}