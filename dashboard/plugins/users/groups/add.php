<?php
if( !$user->isLoggedIn() && !$user->hasPermission($path) ) {
	$user->to('?path=users/login');
} else {
	$title = $language->translate( 'Add' );
	require_once $dash->getInclude( 'header' );
	$form = new Form();

	$plugin = new Plugins();
	$plugins = $plugin->data();

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
			$checked = (!empty( $form($field['id'] ) ) && $form($field['id']) == 'on' || $form($field['id']) == 1 ? 'checked' : '' );

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
	    $post = array(
			'group' => array(
				'capitalize' => true,
				'required' => true,
				'minLength' => 3,
				'unique' => 'groups',
                'remember' => true,
				'name' => 'Group'
			),
			'description' => array(
			    'remember' => true,
				'name' => 'Description'
			),
            'default' => array(
                'remember' => true,
                'maxLength' => 2,
                'checkbox' => true,
                'unique' => 'groups',
                'name' => 'Default'
            )
        );

	    // For all plugins
		foreach( $_POST as $plugin => $field ) {
			if( is_numeric( $plugin ) ) {
				$post[$plugin] = array(
				    'checkbox' => true,
                    'remember' => true,
                    'maxLength' => 2,
                    'name'      => $db->detail('name', 'plugins', 'id', $plugin )
				);
			}
		}

		$validation = $form->check($_POST, $post, [$language, 'translate']);

		if( empty( $form->errors ) ) {
		    // Add group to database or give error message
            $group = new Group();
		    if( $group->add( $validation ) ) {
                $user->to('?path=users/groups/overview&message='.$language->translate('Group has been added').'&messageType=success');
            } else {
		        echo '<div class="error sc-card sc-card-supporting" role="error">'.$language->translate('Something went wrong adding the group').'</div>';
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

        <div class="sc-col sc-xs4">
            <div class="sc-switch" role="switch">
                <label>
                    <span class="sc-tooltip" title="<?php echo $language->translate('Default new user group'); ?>">
                        <?php echo $language->translate('Default'); ?>
                    </span>
                    <input type="checkbox" name="default">
                    <span class="sc-lever"></span>
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