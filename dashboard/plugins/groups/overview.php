<?php
if( !$user->isLoggedIn() ) {
	$user->to('?path=users/login');
} else {
	$title = $language->translate( 'Overview' );
	require_once $dash->getInclude( 'header' );

	$data = $db->select("
SELECT `g`.`id`, `g`.`group`, `g`.`description`, concat( round( ( COUNT(`r`.`id`) / `plugins` ) * 100 ), '%' ) as `rights`
FROM `groups` `g`
JOIN (
    SELECT COUNT(`id`) `plugins`
    FROM `plugins`
    ) `p`
JOIN `rights` `r`
  ON `r`.`groups_id` = `g`.`id`
GROUP BY `g`.`id`
", array(), array('multipleRows'));

	echo '<p class="sc-col sc-xs4 sc-s12"><a href="?path=groups/add" class="sc-raised-button">'.$language->translate('Add group').'</a></p>';
	if( empty( $data ) ) {
		echo $language->translate('No results found');
	} else {
		echo '	<table>
					<thead>
					<tr>
						<th>'.$language->translate('Group').'</th>
						<th>'.$language->translate('Description').'</th>
						<th>'.$language->translate('Rights').'</th>
						<th>'.$language->translate('Options').'</th>
					</tr>
					</thead>
					
					<tbody>';
		foreach( $data as $row => $field ) {
			echo '	<tr>
						<td>'.$field['group'].'</td>
						<td>'.$field['description'].'</td>
						<td>'.$field['rights'].'</td>
						<td>
							<ul>
								<li><a href="?path=groups/edit&id='.$field['id'].'" class="edit">'.$language->translate('Edit').'</a></li>
								<li><a href="?path=groups/delete&id='.$field['id'].'" class="delete">'.$language->translate('Delete').'</a></li>
							</ul>
						</td>
					</tr>';
		}

		echo '		</tbody>';
	}

	require_once $dash->getInclude( 'footer' );
}