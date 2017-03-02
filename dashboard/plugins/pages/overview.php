<?php
if( !$user->isLoggedIn() && !$user->hasPermission($path) ) {
	$user->to('?path=users/login');
} else {
	$title = $language->translate( 'Overview' );
	require_once $dash->getInclude( 'header' );

	$page = new Page();
	$data = $page->data();
	print_r($data);

	echo '	<p class="sc-col sc-xs4 sc-s12">
				<a href="?path=pages/add" class="sc-raised-button">
					<i class="material-icons">add</i>'
					.$language->translate('Add page').' 
				</a>
			</p>';
	if( empty( $data ) ) {
		echo $language->translate('No results found');
	} else {
		echo '	<table>
					<thead>
					<tr>
						<th>'.$language->translate('Page').'</th>
						<th>'.$language->translate('Language').'</th>
						<th>'.$language->translate('Created on').'</th>
						<th>'.$language->translate('Created by').'</th>
						<th>'.$language->translate('Edited by').'</th>
						<th>'.$language->translate('Keywords').'</th>
						<th>'.$language->translate('Options').'</th>
					</tr>
					</thead>
					
					<tbody>';

		// Check if user has permission
		($user->hasPermission('pages/edit') ? $edit = true : $edit = false);
		($user->hasPermission('pages/delete') ? $delete = true : $delete = false);
		foreach( $data as $row => $field ) {
			echo '	<tr>
						<td>'.$field['title'].'</td>
						<td>'.$field['language'].'</td>
						<td>'.$field['create_date'].'</td>
						<td>'.$field['created_by'].'</td>
						<td>'.$field['edited_by'].'</td>
						<td>'.$field['keywords'].'</td>
						<td>
						'.( $edit ? '
							<a href="?path=pages/edit&id='.base64_encode($field['id']).'" class="edit sc-flat-button">
								<i class="material-icons">edit</i>
							</a>' : '').'
						'.( $delete ? '
							<a href="?path=pages/delete&id='.base64_encode($field['id']).'" class="delete sc-flat-button">
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