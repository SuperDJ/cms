<?php
if( !$user->isLoggedIn() ) {
	$user->to('?path=users/login');
} else {
	$title = $language->translate( 'Edit' );
	require_once $dash->getInclude( 'header' );
	$form = new Form();
	$group = new Group();

	$data = $group->data($id);
	$rights = $group->rights($id);

	$plugins = $db->query("SELECT `id`, `name`, `parent` FROM `plugins`");

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

	function plugins( array $plugins, callable $translate, callable $form, array $marked ) {
		$html = '';
		$marks = array_column( $marked, 'plugins_id' );

		foreach( $plugins as $fields => $field ) {

		    // If form input is checked or from rights is checked
			$checked = ( !empty( $form($field['id'] ) ) && $form($field['id']) == 'on' || in_array( $field['id'],  $marks ) ? 'checked' : '' );

			if( !empty( $field['children'] ) ) {
				$html .= '	<li>
								<input type="checkbox" name="'.$field['id'].'" class="sc-checkbox" id="checkbox-'.$field['id'].'" '.$checked.'>
								<label for="checkbox-'.$field['id'].'">'.$translate( $field['name'] ).'</label>
								<ul>';
				$html .= plugins( $field['children'], $translate, $form, $marked );
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
		);

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
		$validation = $form->check($_POST, $post, [$language, 'translate'], $id);

		if( empty( $form->errors ) ) {
			// Add group to database or give error message
			if( $group->edit($validation) ||
                $validation['group'] == $db->detail('group', 'groups', 'id', $id) && $validation['description'] == $db->detail('description', 'groups', 'id', $id) ) {
				// Add rights to database;

				// Set al posted plugins in array
                $posted = array();
                foreach( $_POST as $plugin => $field ) {
                    if( is_numeric( $plugin ) ) {
                        $posted[$plugin] = $field;
                    }
                }
                // Check if plugin rights need to be deleted
				$delete = array();
                foreach( $rights as $key => $field ) {
                    if( !in_array( $field['plugins_id'], array_keys( $posted ) ) ) {
                        $delete[] = $field['plugins_id'];
                    }
                }

				$i = 0;
				$p = 0;
				foreach( $posted as $plugin => $field ) {
                    $validation = $form->check( $_POST, array(
                        $plugin => array(
                            'maxLength' => 2,
                            'name'      => $db->detail('name', 'plugins', 'id', $plugin )
                        )
                    ), [ $language, 'translate' ] );

                    if( empty( $form->errors ) ) {
                        // Insert or delete depending on addition or removal of rights
                        if( in_array( $plugin, $delete ) ) {
                            if( $db->query("DELETE FROM `rights` WHERE `groups_id` = ? AND `plugins_id` = ?", array( $id, $plugin ) ) ) {
                                $p++;
                            }
                        } else {
                            // Check if plugin is already added to group
                            if( in_array( $plugin, array_column( $rights, 'plugins_id' ) ) ) {
                                $p++;
                            } else if( $db->query( "INSERT INTO `rights` (`groups_id`, `plugins_id`) VALUES (?, ?)", array( $id, $plugin ) ) ) {
								$p++;
							}

						}
                    } else {
                        echo $form->outputErrors();
                    }
					$i++;
				}

				if( $i === $p ) {
					$user->to('?path=groups/overview&message='.$language->translate('Group has been edited').'&messageType=success');
				} else {
					echo '<div class="error sc-card sc-card-supporting" role="error">'.$language->translate('Something went wrong edited the group rights').'</div>';
				}
			} else {
				echo '<div class="error sc-card sc-card-supporting" role="error">'.$language->translate('Something went wrong editing the group').'</div>';
			}
		} else {
			echo $form->outputErrors();
		}
	}
	?>
	<form action="" method="post">
		<div class="sc-floating-input">
			<input type="text" name="group" id="group" required value="<?php echo ( !empty( $form->input('group') ) ? $form->input('group') : $data[0]['group'] ); ?>">
			<label for="group"><?php echo $language->translate('Group'); ?></label>
		</div>

		<div class="sc-multi-input">
			<textarea name="description" id="description"><?php echo ( !empty( $form->input('description') ) ? $form->input('description') : $data[0]['description'] ); ?></textarea>
			<label for="description"><?php echo $language->translate('Description'); ?></label>
		</div>

		<div class="sc-col sc-xs4 sc-s12">
			<ul>
				<?php
				echo plugins( buildTree($plugins), [$language, 'translate'], [$form, 'input'], $rights );
				?>
			</ul>
		</div>

		<button class="sc-raised-button"><i class="material-icons">save</i> <?php echo $language->translate('Save'); ?></button>
	</form>
	<?php
	require_once $dash->getInclude( 'footer' );
}