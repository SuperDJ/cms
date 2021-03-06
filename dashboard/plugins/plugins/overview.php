<?php
if( !$user->isLoggedIn() && !$user->hasPermission($path) ) {
	$user->to('?path=users/login');
} else {
	$title = $language->translate( 'Overview' );
	require_once $dash->getInclude( 'header' );
	$plugins = new Plugins( $db );
	$data = $plugins->data();

	echo '	<p class="sc-col sc-xs4 sc-s12">
				<a href="?path=pages/add" class="sc-raised-button">
					<i class="material-icons">add</i>'
					.$language->translate('Add plugin').' 
				</a>
			</p>';
	if( empty( $data ) ) {
		echo $language->translate('No results found');
	} else {
		echo '	<table>
					<thead>
					<tr>
						<th>'.$language->translate('Plugin').'</th>
						<th>'.$language->translate('icon').'</th>
						<th>'.$language->translate('Sort').'</th>
						<th>'.$language->translate('Options').'</th>
					</tr>
					</thead>
					
					<tbody>';

		// Check if user has permission
		($user->hasPermission('plugins/edit') ? $edit = true : $edit = false);
		($user->hasPermission('plugins/delete') ? $delete = true : $delete = false);
		foreach( $data as $row => $field ) {
			if( $field['parent'] == 0 ) {
				echo '	<tr>
							<td>'.$field['name'].'</td>
							<td><i class="material-icons">'.$field['icon'].'</i></td>
							<td>'.$field['sort'].'</td>
							<td>
							'.( $edit ? '
								<a href="?path=plugins/edit&id='.base64_encode( $field['id'] ).'" class="edit sc-flat-button">
									<i class="material-icons">edit</i>
								</a>' : '' ).'
							'.( $delete ? '
								<a href="?path=plugins/delete&id='.base64_encode( $field['id'] ).'" class="delete sc-flat-button">
									<i class="material-icons">delete</i>
								</a>' : '' ).'	
							</td>
						</tr>';
			}
		}

		echo '		</tbody>
				</table>';
	}

	require_once $dash->getInclude( 'footer' );
}