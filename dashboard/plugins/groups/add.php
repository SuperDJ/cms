<?php
if( !$user->isLoggedIn() ) {
	$user->to('?path=users/login');
} else {
	$title = $language->translate( 'Add' );
	require_once $dash->getInclude( 'header' );
	$form = new Form();

	$plugins = $db->select("SELECT `id`, `name`, `parent` FROM `plugins`");

	function buildTree( array $elements, $parentId = 0 ) {
		$branch = array();

		foreach( $elements as $element ) {
			if( $element['parent'] == $parentId ) {
				$children = buildTree( $elements, $element['id'] );

				if( $children ) {
					$element['children'] = $children;
				}

				$branch[] = $element;
			}
		}

		return $branch;
	}

	function plugins( array $plugins, callable $translate, callable $form ) {
		$html = '';

		foreach( $plugins as $fields => $field ) {
			$checked = (!empty( $form($field['id'] ) ) && $form($field['id']) == 'on' ? 'checked' : '' );

			if( !empty( $field['children'] ) ) {
				$html .= '	<li>
								<input type="checkbox" name="'.$field['id'].'" class="sc-checkbox" id="checkbox-'.$field['id'].'" '.$checked.'>
								<label for="checkbox-'.$field['id'].'">'.$translate( $field['name'] ).'</label>
								<ul>';
				$html .= plugins( $field['children'], $translate, $form );
				$html .= '</ul>';
			} else {
				$html .= '	<li>
								<input type="checkbox" name="'.$field['id'].'" class="sc-checkbox" id="checkbox-'.$field['id'].'" '.$checked.'>
								<label for="checkbox-'.$field['id'].'">'.$translate( $field['name'] ).'</label>
							</li>';
			}
		}

		return $html;
	}

	if( $_POST ) {
		$validation = $form->check($_POST, array(
		    'group' => array(
		        'capitalize' => true,
		        'required' => true,
                'minLength' => 3,
                'unique' => 'groups',
                'name' => $language->translate('Group')
            ),
            'description' => array(
                'name' => $language->translate('Description')
            ),
            'default' => array(
                'name' => $language->translate('Default group'),
                'unique' => 'groups',
                'maxLength' => 3
            )
        ), [$language, 'translate']);

		die();

		if( empty( $form->errors ) ) {
		    // Add group to database or give error message
		    if( $db->insert("INSERT INTO `groups` (`group`, `description`, `default`) VALUES (?, ?, ?)", $validation) ) {
                // Add rights to database
                // Get insert id from database
                $id = $db->detail('id', 'groups', 'group', $validation['group']);

                $i = 0;
                $p = 0;
                foreach( $_POST as $plugin => $field ) {
                    echo $plugin;
                    if( is_numeric( $plugin ) ) {
                        $i++;
						$validation = $form->check( $_POST, array(
							$plugin => array(
								'maxLength' => 2,
								'name'      => $language->translate( $db->detail('name', 'plugins', 'id', $plugin ) )
							)
						), [ $language, 'translate' ] );

						if( empty( $form->errors ) ) {
							if( $db->insert( "INSERT INTO `rights` (`groups_id`, `plugins_id`) VALUES (?, ?)", array( $id, $plugin ) ) ) {
							    $p++;
							}
						} else {
							echo $form->outputErrors();
						}
					}
                }

                if( $i === $p ) {
                    $user->to('?path=groups/overview&message='.$language->translate('Group has been added').'&messageType=success');
                } else {
					echo '<div class="error sc-card sc-card-supporting">'.$language->translate('Something went wrong adding the group rights').'</div>';
                }
            } else {
		        echo '<div class="error sc-card sc-card-supporting">'.$language->translate('Something went wrong adding the group').'</div>';
            }
        } else {
		    echo $form->outputErrors();
        }
	}
?>
	<form action="" method="post">
		<div class="sc-floating-input">
			<input type="text" name="group" id="group" required value="<?php echo $form->input('group'); ?>">
			<label for="group"><?php echo $language->translate('Group'); ?></label>
		</div>

		<div class="sc-multi-input">
			<textarea name="description" id="description"><?php echo $form->input('description'); ?></textarea>
			<label for="description"><?php echo $language->translate('Description'); ?></label>
		</div>

        <div class="sc-xs">
            <label for="default"><?php echo $language->translate('Default group'); ?></label>
            <div class="sc-switch">
                <label>
                    <?php echo $language->translate('Yes'); ?>
                    <input type="checkbox" name="default-group" id="default">
                    <span class="sc-lever"></span>
					<?php echo $language->translate('No'); ?>
                </label>
            </div>

        </div>

		<div class="sc-col sc-xs4 sc-s12">
			<ul>
			<?php
			echo plugins( buildTree($plugins), [$language, 'translate'], [$form, 'input'] );
			?>
			</ul>
		</div>

		<button class="sc-raised-button"><i class="material-icons">save</i> <?php echo $language->translate('Save'); ?></button>
	</form>
<?php
	require_once $dash->getInclude( 'footer' );
}