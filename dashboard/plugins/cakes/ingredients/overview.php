<?php
if( !$user->isLoggedIn() && !$user->hasPermission($path) ) {
	$user->to('?path=users/login');
} else {
	$title = $language->translate( 'Overview' );
	require_once $dash->getInclude( 'header' );

	$cake = new Cake();
	$data = $cake->dataIngredient();

	echo '	<p class="sc-col sc-xs4 sc-s12">
				<a href="?path=cakes/ingredients/add" class="sc-raised-button">
					<i class="material-icons">add</i>'
					.$language->translate('Add ingredient').'
				</a>
			</p>';
	if( empty( $data ) ) {
		echo $language->translate('No results found');
	} else {
		echo '	<table>
					<thead>
					<tr>
						<th>'.$language->translate('Ingredient').'</th>
						<th>'.$language->translate('Calories').'</th>
						<th>'.$language->translate('Allergies').'</th>
						<th>'.$language->translate('Buy price').'</th>
						<th>'.$language->translate('Sell price').'</th>
						<th>'.$language->translate('Stock').'</th>
						<th>'.$language->translate('Options').'</th>
					</tr>
					</thead>
					
					<tbody>';

		// Check if user has permission
		($user->hasPermission('cakes/ingredients/delete') ? $delete = true : $delete = false);
		($user->hasPermission('cakes/ingredients/edit') ? $edit = true : $edit = false);
		foreach( $data as $row => $field ) {
			echo '	<tr>
						<td>'.$language->translate($field['ingredient']).'</td>
						<td>'.$field['calories'].'</td>
						<td>'.$field['allergies'].'</td>
						<td>&euro; '.number_format( $field['buy_price'], 2, ',', '.' ).'</td>
						<td>&euro; '.number_format( $field['sell_price'], 2, ',', '.' ).'</td>
						<td>'.$field['stock'].$field['unit'].'</td>
						<td>
						'.( $edit && !$db->exists('id', 'users', 'groups_id', $field['id']) ? '
							<a href="?path=cakes/ingredients/edit&id='.base64_encode($field['id']).'" class="edit sc-flat-button">
								<i class="material-icons">edit</i>
							</a>' : '').'
						'.( $delete && !$db->exists('id', 'users', 'groups_id', $field['id']) ? '
							<a href="?path=cakes/ingredients/delete&id='.base64_encode($field['id']).'" class="delete sc-flat-button">
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