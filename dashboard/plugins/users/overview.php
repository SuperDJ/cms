<?php
if( !$user->isLoggedIn() ) {
	$user->to('?path=users/login');
} else {
	$title = $language->translate('Overview');
	require_once $dash->getInclude('header');
	$data = $db->select("SELECT `id`, `first_name`, `last_name`, `email`, `register_date`, `active_date`, `active` FROM `users`");

	echo '    <table class="sc-table-hover">
				<thead>
					<tr>
						<th>'.$language->translate('First name').'</th>
						<th>'.$language->translate('Last name').'</th>
						<th>'.$language->translate('Email').'</th>
						<th>'.$language->translate('Register date').'</th>
						<th>'.$language->translate('Last active').'</th>
						<th>'.$language->translate('Active').'</th>
						<th>'.$language->translate('Options').'</th>
					</tr>
				</thead>
				<tbody>';
	foreach( $data as $key => $field ) {
		echo '		<tr>
						<td>'.$field['first_name'].'</td>
						<td>'.$field['last_name'].'</td>
						<td>'.$field['email'].'</td>
						<td>'.$field['register_date'].'</td>
						<td>'.$field['active_date'].'</td>
						<td>'.( $field['active'] == 1 ? '<i class="material-icons success">check</i>' : '<i class="material-icons error">clear</i>' ).'</td>
						<td>
							<ul>
								<li><a href="?path=users/edit&id='.$field['id'].'" class="edit">'.$language->translate('Edit').'</a></li>
								<li><a href="?path=users/delete&id='.$field['id'].'" class="delete">'.$language->translate('Delete').'</a></li>
							</ul>
						</td>
					</tr>';
	}
	echo '      </tbody>
            </table>';

	require_once $dash->getInclude('footer');
}