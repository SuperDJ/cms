<?php
if( !$user->isLoggedIn() && !$user->hasPermission($path) ) {
	$user->to('?path=users/login');
} else {
    if( empty( $id ) && !$db->exists('id', 'ingredients', 'id', $id) ) {
        $user->to('?path=cakes/ingredients/overview');
    } else {
		$cake = new Cake();
		$data = $cake->dataIngredient( $id )[0];
		$title = $language->translate( 'Edit' ).': '.$language->translate( $data['ingredient'] );
		require_once $dash->getInclude( 'header' );
		$form = new Form();

		if( $_POST ) {
			$validation = $form->check( $_POST, array(
				'ingredient' => array(
					'required'  => true,
					'remember'  => true,
					'unique'    => 'ingredients',
					'minLength' => 4,
					'name'      => 'Ingredient'
				),
				'calories'   => array(
					'required' => true,
					'remember' => true,
					'numeric'  => true,
					'name'     => 'Calories'
				),
				'allergies'  => array(
					'remember'  => true,
					'minLength' => 4,
					'name'      => 'Allergies'
				),
				'buy_price'  => array(
					'required' => true,
					'remember' => true,
					'name'     => 'Buy price'
				),
				'sell_price' => array(
					'required' => true,
					'remember' => true,
					'name'     => 'Sell price'
				),
				'stock'      => array(
					'required' => true,
					'remember' => true,
					'numeric'  => true,
					'name'     => 'Stock'
				),
				'unit'       => array(
					'minLength' => 2,
					'maxLength' => 2,
					'required'  => true,
					'remember'  => true,
					'name'      => 'Unit'
				)
			), [ $language, 'translate' ], $id );

			if( empty( $form->errors ) ) {
				if( $cake->editIngredient( $validation ) ) {
					$user->to( '?path=cakes/ingredients/overview&message='.$language->translate( 'Ingredient has been edited' ).'&messageType=success' );
				} else {
					echo '<div class="error sc-card sc-card-supporting">'.$language->translate( 'Ingredient has not been edited' ).'</div>';
				}
			} else {
				echo $form->outputErrors();
			}
		}
		?>

        <form action="" method="post">
            <div class="sc-floating-input">
                <input type="text" name="ingredient" id="ingredient" required
                       value="<?php echo( !empty( $form->input( 'ingredient' ) ) ? $form->input( 'ingredient' ) : $data['ingredient'] ); ?>">
                <label for="ingredient"><?php echo $language->translate( 'Ingredient' ); ?></label>
            </div>

            <div class="sc-floating-input">
                <input type="number" name="calories" id="calories" required
                       value="<?php echo( !empty( $form->input( 'calories' ) ) ? $form->input( 'calories' ) : $data['calories'] ); ?>">
                <label for="calories"><?php echo $language->translate( 'Calories' ); ?></label>
            </div>

            <div class="sc-floating-input">
                <input type="text" name="allergies" id="allergies"
                       value="<?php echo( !empty( $form->input( 'allergies' ) ) ? $form->input( 'allergies' ) : $data['allergies'] ); ?>">
                <label for="allergies"><?php echo $language->translate( 'Allergies' ); ?></label>
            </div>

            <div class="sc-floating-input">
                <input type="text" name="buy_price" id="buy_price" required
                       value="<?php echo( !empty( $form->input( 'buy_price' ) ) ? $form->input( 'buy_price' ) : $data['buy_price'] ); ?>">
                <label for="buy_price"><?php echo $language->translate( 'Buy price' ); ?></label>
            </div>

            <div class="sc-floating-input">
                <input type="text" name="sell_price" id="sell_price" required
                       value="<?php echo( !empty( $form->input( 'sell_price' ) ) ? $form->input( 'sell_price' ) : $data['sell_price'] ); ?>">
                <label for="sell_price"><?php echo $language->translate( 'Sell price' ); ?></label>
            </div>

            <div class="sc-floating-input">
                <input type="number" name="stock" id="stock" required
                       value="<?php echo( !empty( $form->input( 'stock' ) ) ? $form->input( 'stock' ) : $data['stock'] ); ?>">
                <label for="stock"><?php echo $language->translate( 'Stock' ); ?></label>
            </div>

            <select name="unit" id="unit" class="sc-select">
                <option value="kg"><?php echo $language->translate( 'Kilogram' ); ?></option>
                <option value="gr"><?php echo $language->translate( 'Gram' ); ?></option>
                <option value="kg"><?php echo $language->translate( 'Kilogram' ); ?></option>
                <option value="dl"><?php echo $language->translate( 'Deciliter' ); ?></option>
                <option value="ml"><?php echo $language->translate( 'Milliliter' ); ?></option>
                <option value="l"><?php echo $language->translate( 'Liter' ); ?></option>
            </select>

            <button class="sc-raised-button"><i
                    class="material-icons">save</i> <?php echo $language->translate( 'Save' ); ?></button>
        </form>
		<?php
		require_once $dash->getInclude( 'footer' );
	}
}