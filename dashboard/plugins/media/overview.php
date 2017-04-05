<?php
if( !$user->isLoggedIn() && !$user->hasPermission($path) ) {
	$user->to('?path=users/login');
} else {
	$title = $language->translate( 'Overview' );
	require_once $dash->getInclude( 'header' );
	$media = new Media($db);
	$data = $media->data();

	echo '  <style>img, video, audio {max-width: 250px;}</style>
			<p class="sc-col sc-xs4 sc-s12">
				<a href="?path=media/add" class="sc-raised-button">'.$language->translate('Add file').'</a>
			</p>';

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
						<th>'.$language->translate('URL').'</th>
						<th>'.$language->translate('Options').'</th>
					</tr>
					</thead>
				
					<tbody>';

		// Check if user has permission
		($user->hasPermission('groups/edit') ? $edit = true : $edit = false);
		($user->hasPermission('groups/delete') ? $delete = true : $delete = false);
		foreach( $data as $row => $field ) {
			echo '	<tr>
						<td>';
			switch( explode( '/', $field['mime'] )[0] ) {
				case 'image':
					echo '<img src="'.$field['path'].'">';
					break;
				case 'video':
					echo '	<video controls>
								<source src="'.$field['path'].'" type="'.$field['mime'].'">
								'.$language->translate('Your browser does not support video').'
							</video>';
					break;
				case 'audio':
					echo '	<audio controls>
								<source src="'.$field['path'].'" type="'.$field['mime'].'">
								'.$language->translate('Your browser does not support audio').'
							</audio>';
					break;
				case 'application':
					echo '<a href="'.$field['path'].'"><i class="material-icons">insert_drive_file</i></a>';
			}
			echo '		</td>
						<td>'.$field['description'].'</td>
						<td>'.$language->translate(ucfirst( explode( '/', $field['mime'] )[0] )).'</td>
						<td>'.$field['upload_date'].'</td>
						<td>'.$field['path'].'</td>
						<td>
						'.( $edit ? '
							<a href="?path=media/edit&id='.base64_encode($field['id']).'" class="edit sc-flat-button">
								<i class="material-icons">edit</i>
							</a>' : '').'
						'.( $delete ? '
							<a href="?path=media/delete&id='.base64_encode($field['id']).'" class="delete sc-flat-button">
								<i class="material-icons">delete</i>
							</a>' : '').'	
						</td>';

		}
	}

	require_once $dash->getInclude( 'footer' );
}