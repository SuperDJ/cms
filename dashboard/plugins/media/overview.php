<?php
if( !$user->isLoggedIn() ) {
	$user->to('?path=users/login');
} else {
	$title = $language->translate( 'Overview' );
	require_once $dash->getInclude( 'header' );

	$data = $db->select("SELECT `id`, `path`, `mime`, `upload_date`, `title`, `description` FROM `files`", array('multipleRows'));

	echo '<p class="sc-col sc-xs4 sc-s12"><a href="?path=media/add" class="sc-raised-button">'.$language->translate('Add file').'</a></p>';

	if( empty( $data ) ) {
		echo $language->translate('No results found');
	} else {
		echo '	<table>
					<thead>
					<tr>
						<th>'.$language->translate('File').'</th>
						<th>'.$language->translate('Description').'</th>
						<th>'.$language->translate('Type').'</th>
						<th>'.$language->translate('Upload date').'</th>
						<th>'.$language->translate('Options').'</th>
					</tr>
					</thead>
				
					<tbody>';
		foreach( $data as $row => $field ) {
			echo '	<tr>
						<td></td>
						<td>'.$field['description'].'</td>
						<td>'.explode( '/', $field['mime'] )[0].'</td>
						<td>'.$field['upload_date'].'</td>
						<td>
							<a href="?path=media/edit&id='.base64_encode($field['id']).'" class="edit sc-flat-button">
								<i class="material-icons">edit</i>
							</a>	
							<a href="?path=media/delete&id='.base64_encode($field['id']).'" class="delete sc-flat-button">
								<i class="material-icons">delete</i>
							</a>
						</td>';

		}
	}

	require_once $dash->getInclude( 'footer' );
}