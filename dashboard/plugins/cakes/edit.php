<?php
if( !$user->isLoggedIn() && !$user->hasPermission($path) ) {
	$user->to('?path=users/login');
} else {
    if( empty( $id ) && !$db->exists('id', 'cakes', 'id', $id) ) {
        $user->to('?path=cakes/overview');
    } else {
		$form = new Form();
		$cake = new Cake();
		$data = $cake->data( $id )[0];
		$ingredients = $cake->dataIngredient();
		$recipes = $cake->dataRecipe($id);
		$title = $language->translate('Edit').': '.$language->translate($data['cake']);
		require_once $dash->getInclude('header');

		if( $_POST ) {
			$validation = $form->check( $_POST, array(
				'cake'        => array(
					'required'  => true,
					'remember'  => true,
					'minLength' => 4,
					'name'      => 'Cake'
				),
				'description' => array(
					'remember' => true,
					'name'     => 'Description'
				),
				'path'        => array(
					'required' => true,
					'remember' => true,
					'exists'   => 'files',
					'name'     => 'Image'
				),
				'recipe' => array(
					'required' => true,
					'name' => 'Recipe'
				)
			), [ $language, 'translate' ], $id );

			if( empty( $form->errors ) ) {
				if( $cake->edit( $validation ) ) {
				    die();
					$user->to( '?path=cakes/overview&message='.$language->translate( 'Cake has been edited' ).'&messageType=success' );
				} else {
					echo '<div class="error sc-card sc-card-supporting">'.$language->translate( 'Cake has not been edited' ).'</div>';
				}
			} else {
				echo $form->outputErrors();
			}
		}
		?>

        <form action="" method="post">
            <div class="sc-floating-input">
                <input type="text" name="cake" id="cake" required value="<?php echo( !empty( $form->input( 'cake' ) ) ? $form->input( 'cake' ) : $data['cake'] ); ?>">
                <label for="cake"><?php echo $language->translate( 'Cake' ); ?></label>
            </div>

            <div class="sc-multi-input">
                <textarea name="description" id="description" required><?php echo( !empty( $form->input( 'description' ) ) ? $form->input( 'description' ) : $data['description'] ); ?></textarea>
                <label for="description"><?php echo $language->translate( 'Description' ); ?></label>
            </div>

            <div class="sc-col sc-xs4">
				<?php
				echo
					$language->translate('Take a look at').
					' <a href="?path=media/overview" target="_blank">'.$language->translate('media').'</a> '
					.$language->translate('and URL where type is image. Paste the URL in the field below');
				?>
            </div>

            <div class="sc-floating-input">
                <input type="text" name="path" id="image" required value="<?php echo( !empty( $form->input( 'path' ) ) ? $form->input( 'path' ) : $data['image'] ); ?>">
                <label for="image"><?php echo $language->translate( 'Image' ); ?></label>
            </div>

            <div class="sc-col sc-xs4">
				<?php echo $language->translate('Recipe').':'; ?>
                <ul>
					<?php
					foreach( $ingredients as $key => $field ) {
                        echo '      <li>';
					    if( !empty( $recipes ) && in_array( $field['id'], array_column( $recipes, 'ingredients_id' ) ) ) {
                            echo '      <input type="checkbox" class="sc-checkbox" name="recipe[]" id="'.$field['ingredient'].'" value="'.$field['id'].'" checked>';
                        } else {
					        echo '      <input type="checkbox" class="sc-checkbox" name="recipe[]" id="'.$field['ingredient'].'" value="'.$field['id'].'">';
                        }
                        echo '          <label for="'.$field['ingredient'].'">'.$language->translate( $field['ingredient'] ).'</label>
                                    </li>';
					}
					?>
                </ul>
            </div>

            <button class="sc-raised-button"><i class="material-icons">save</i> <?php echo $language->translate( 'Save' ); ?></button>
        </form>
		<?php
		require_once $dash->getInclude( 'footer' );
	}
}