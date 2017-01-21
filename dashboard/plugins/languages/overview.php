<?php
if( !$user->isLoggedIn() ) {
	$user->to('?path=users/login');
} else {
	$title = $language->translate( 'Overview' );
	require_once $dash->getInclude( 'header' );

	$data = $db->select("
SELECT `l`.`id`, `l`.`language`, `l`.`iso_code`, concat( round( 100 * count(`t`.`languages_id`) / `t2`.`cnt`, 0 ), '%') AS `translated`
FROM `languages` `l`
  LEFT JOIN `translations` `t`
    ON `l`.`id` = `t`.`languages_id`
  CROSS JOIN (
               SELECT count(`id`) `cnt`
               FROM `translations`
               WHERE `languages_id` = 1
             ) `t2`
GROUP BY `l`.`id`;
", array(), array('multipleRows')); // TODO Get `languages_id` from database

	echo '<p class="sc-col sc-xs4 sc-s12"><a href="?path=languages/add" class="sc-raised-button">'.$language->translate('Add language').'</a></p>';

	if( !empty( $data ) ) {
		echo '	<table class="sc-table-hover">
					<thead>
						<tr>
							<th>'.$language->translate( 'Language' ).'</th>
							<th>'.$language->translate( 'ISO code' ).'</th>
							<th>'.$language->translate('Translated').'</th>	
							<th>'.$language->translate( 'Options' ).'</th>
						</tr>
					</thead>
					<tbody>';
		foreach( $data as $key => $field ) {
			echo '		<tr>
							<td>'.$language->translate( $field['language'] ).'</td>
							<td>'.$field['iso_code'].'</td>
							<td>'.$field['translated'].'</td>
							<td>
								<ul>
									<li><a href="?path=languages/translate&id='.$field['id'].'">'.$language->translate('Translate').'</a></li>
									<li><a href="?path=languages/edit&id='.$field['id'].'" class="edit">'.$language->translate('Edit').'</a></li>
									<li><a href="?path=languages/delete&id='.$field['id'].'" class="delete">'.$language->translate('Delete').'</a></li>
								</ul>
							</td>
						</tr>';
		}
		echo '		</tbody>
			</table>';
	} else {
		echo $language->translate('No results found');
	}

	require_once $dash->getInclude( 'footer' );
}