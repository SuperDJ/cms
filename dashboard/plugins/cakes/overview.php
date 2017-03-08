<?php
if( !$user->isLoggedIn() && !$user->hasPermission($path) ) {
	$user->to('?path=users/login');
} else {
	$title = $language->translate( 'Overview' );
	require_once $dash->getInclude( 'header' );

	$cake = new Cake();
	$data = $cake->data();

	echo '	<p class="sc-col sc-xs4 sc-s12">
				<a href="?path=cakes/add" class="sc-raised-button">
					<i class="material-icons">add</i>
					'.$language->translate('Add cake').'
				</a>
			</p>';
	if( empty( $data ) ) {
		echo $language->translate('No results found');
	} else {
		echo '	<table>
					<thead>
					<tr>
						<th>'.$language->translate('Cake').'</th>
						<th>'.$language->translate('Description').'</th>
						<th>'.$language->translate('Calories').'</th>
						<th>'.$language->translate('Buy price').'</th>
						<th>'.$language->translate('Sell price').'</th>
						<th>'.$language->translate('Options').'</th>
					</tr>
					</thead>
					
					<tbody>';

		// Check if user has permission
		($user->hasPermission('cakes/delete') ? $delete = true : $delete = false);
		($user->hasPermission('cakes/edit') ? $edit = true : $edit = false);
		foreach( $data as $row => $field ) {
			echo '	<tr>
						<td>'.$field['cake'].'</td>
						<td>'.$field['description'].'</td>
						<td>'.$field['calories'].'</td>
						<td>&euro; '.number_format( $field['buy_price'], 2, ',', '.' ).'</td>
						<td>&euro; '.number_format( $field['sell_price'], 2, ',', '.' ).'</td>
						<td>
						'.( $edit && !$db->exists('id', 'users', 'groups_id', $field['id']) ? '
							<a href="?path=cakes/edit&id='.base64_encode($field['id']).'" class="edit sc-flat-button">
								<i class="material-icons">edit</i>
							</a>' : '').'
						'.( $delete && !$db->exists('id', 'users', 'groups_id', $field['id']) ? '
							<a href="?path=cakes/delete&id='.base64_encode($field['id']).'" class="delete sc-flat-button">
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