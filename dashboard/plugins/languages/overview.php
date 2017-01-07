<?php
if( !$user->isLoggedIn() ) {
	$user->to('?path=users/login');
} else {
	$title = $language->translate( 'Overview' );
	require_once $dash->getInclude( 'header' );

	$data = $language->data();
	print_r($data);

	echo '<p class="sc-xs4 sc-s12"><a href="?path=languages/add" class="sc-raised-button">'.$language->translate('Add language').'</a></p>';

	if( !empty( $data ) ) {
		echo '	<table class="sc-table-hover">
				<thead>
					<tr>
						<th>'.$language->translate( 'Language' ).'</th>
						<th>'.$language->tranlsate( 'ISO code' ).'</th>	
					</tr>
				</thead>
				<tbody>';
		foreach( $data as $key => $field ) {
			echo '		<tr>
						<td>'.$language->translate( $field['language'] ).'</td>
						<td>'.$field['iso_code'].'</td>
					</tr>';
		}
		echo '		</tbody>
			</table>';
	} else {
		echo $language->translate('No results found');
	}

	require_once $dash->getInclude( 'footer' );
}