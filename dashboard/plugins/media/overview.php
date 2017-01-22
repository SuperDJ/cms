<?php
if( !$user->isLoggedIn() ) {
	$user->to('?path=users/login');
} else {
	$title = $language->translate( 'Overview' );
	require_once $dash->getInclude( 'header' );

	$data = $db->select("SELECT `id`, `path`, `mime`, `upload_date`, `title`, `description` FROM `files`");

	echo '<p class="sc-col sc-xs4 sc-s12"><a href="?path=media/add" class="sc-raised-button">'.$language->translate('Add file').'</a></p>';

	if( empty( $data ) ) {
		echo $language->translate('No results found');
	} else {
		echo '	<table>
					<thead>
					<tr>
						<th>'.$language->translate('File').'</th>
						<th>'.$language->translate('').'</th>
					</tr>
				</thead>';
		foreach( $data as $row => $field ) {

		}
	}

	require_once $dash->getInclude( 'footer' );
}