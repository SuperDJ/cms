<?php
if( !$user->isLoggedIn() && !$user->hasPermission($path) ) {
	$user->to('?path=users/login');
} else {
	if( empty( $id ) && !$db->exists('id', 'pages', 'id', $id) ) {
		$user->to('?path=pages/overview');
	} else {
		$page = new Page();
		$data = $page->data( $id );
		$title = $language->translate( 'Edit' );
		require_once $dash->getInclude( 'header' );
		$form = new Form();

		if( $_POST ) {
			$validation = $form->check( $_POST, array(
				'title'    => array(
					'minLength' => 5,
					'name'      => 'Title'
				),
				'content'  => array(
					'name' => 'Content'
				),
				'language' => array(
					'numeric' => true,
					'name'    => 'Language'
				),
				'keywords' => array(
					'name' => 'Keywords'
				)
			), [ $language, 'translate' ], null, true );

			if( empty( $form->errors ) ) {
				$page = new Page();
				if( $page->add( $validation ) ) {
					$user->to( '?path=pages/overview&message='.$language->translate( 'Page added' ).'&messageType=success' );
				} else {
					echo '<div class="error sc-card sc-card-supporting">'.$language->translate( 'Could not add page' ).'</div>';
				}
			} else {
				echo $form->outputErrors();
			}
		}
		?>
		<form action="" method="post">
			<div class="sc-floating-input">
				<input type="text" name="title" id="title" required value="<?php echo( !empty( $form->input( 'title' ) ? $form->input( 'title' ) : $data[0]['title'] ) ); ?>">
				<label for="title"><?php echo $language->translate( 'Title' ); ?></label>
			</div>

			<div class="sc-col sc-xs4">
				<textarea name="content" id="content"><?php echo ( !empty( $form->input('content') ) ? $form->input('content') : $data[0]['content'] ); ?></textarea>
			</div>

			<select name="language" id="language" class="sc-select">
				<?php
				foreach( $language->data() as $row => $field ) {
					echo '<option value="'.$field['id'].'">'.$language->translate( $field['language'] ).'</option>';
				}
				?>
			</select>

			<div class="sc-multi-input">
            <textarea name="keywords" id="keywords">
                <?php echo( !empty( $form->input( 'keywords' ) ) ? $form->input( 'keywords' ) : $data[0]['keywords'] ); ?>
            </textarea>

				<label for="keywords" class="sc-tooltip"
					   title="<?php echo $language->translate( 'Words or small sentence for search engines' ); ?>">
					<?php echo $language->translate( 'Keywords' ).
						'<em>('.$language->translate( 'Separated by comma' ).')</em>'; ?>
				</label>
			</div>

			<button class="sc-raised-button"><i
					class="material-icons">save</i><?php echo $language->translate( 'Save' ); ?></button>
		</form>

		<script src="/dashboard/js/ckeditor/ckeditor.js"></script>
		<script>
			CKEDITOR.replace( 'content', {
					customConfig: '/dashboard/js/ckeditor-config.js'
				}
			);

		</script>
		<?php

		require_once $dash->getInclude( 'footer' );
	}
}
?>