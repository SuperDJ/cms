<?php
if( !$user->isLoggedIn() && !$user->hasPermission($path) ) {
	$user->to('?path=overview/overview');
} else {
	// Define page title
	$title = $language->translate('Edit profile').': '.substr( $user->data['first_name'], 0, 1 ).'. '.$user->data['last_name'];
	require_once $dash->getInclude('header');

	$form = new Form($db);

	if( $_POST ) {
	    // TODO add timezone, profile image
	    $validation = $form->check($_POST, array(
			'first_name'         => array(
				'required'  => true,
				'minLength' => 3,
				'remember'  => true,
				'name'      => 'First name'
			),
			'last_name'          => array(
				'required'  => true,
				'minLength' => 4,
				'remember'  => true,
				'name'      => 'Last name'
			),
			'email'              => array(
				'unique'   => 'users',
				'required' => true,
				'email'    => true,
				'remember' => true,
				'name'     => 'Email'
			),
            'language' => array(
                'numeric' => true,
                'exists' => 'languages',
                'remember' => true,
                'name' => 'Language'
            ),
            'picture' => array(
                'name' => 'Profile image'
            )
        ), [$language, 'translate'], $session->get('user')['id']);

	    if( empty( $form->errors ) ) {
	        if( $user->profile($validation) ) {
	            $user->to('?path=users/profile&message='.$language->translate('Profile edited').'&messageType=success');
            } else {
	            echo '<div class="alert sc-card sc-card-supporting">'.$language->translate('Something went wrong editing your profile').'</div>';
            }
        } else {
	        echo $form->outputErrors();
        }
    }
?>
    <div class="sc-row">
        <form action="" method="post">
            <div class="sc-floating-input">
                <input type="text" name="first_name" id="first_name" required value="<?php echo ( !empty( $form->input('first_name') ) ? $form->input('first_name') : $user->data['first_name'] ); ?>">
                <label for="first_name"><?php echo $language->translate('First name'); ?></label>
            </div>

            <div class="sc-floating-input">
                <input type="text" name="last_name" id="last_name" required value="<?php echo ( !empty( $form->input('last_name') ) ? $form->input('last_name') : $user->data['last_name'] ); ?>">
                <label for="last_name"><?php echo $language->translate('Last name'); ?></label>
            </div>

            <div class="sc-floating-input">
                <input type="email" name="email" id="email" required value="<?php echo ( !empty( $form->input('email') ) ? $form->input('email') : $user->data['email'] ); ?>">
                <label for="email"><?php echo $language->translate('Email'); ?></label>
            </div>

            <select name="language" id="language" class="sc-select">
                <?php
                echo '  <option value="'.( !empty( $form->input('language') ) ? $form->input('language') : $user->data['languages_id'] ).'">'
                            .$language->translate($db->detail('language', 'languages', 'id', ( !empty( $form->input('language') ) ? $form->input('language') : $user->data['languages_id'] ))).
                        '</option>';

                foreach( $language->data() as $row => $field ) {
                    if( $field['id'] != $user->data['languages_id'] ) {
                        echo '<option value="'.$field['id'].'">'.$language->translate($field['language']).'</option>';
                    }
                }
                ?>
            </select>

            <div class="sc-col sc-xs4">
                <?php
                if( !empty( $user->data['facebook_picture'] ) ) {
                    echo '  <input type="radio" class="sc-radio" value="facebook_picture" name="picture" id="facebook_picture"'.( $user->data['picture'] == 'facebook_picture' ? 'checked' : '' ).'>
                            <label for="facebook_picture">'.$language->translate('Facebook picture').'</label>';
                }

                if( !empty( $user->data['google_picture'] ) ) {
                    echo '  <input type="radio" class="sc-radio" value="google_picture" name="picture" id="google_picture" '.( $user->data['picture'] == 'google_picture' ? 'checked' : '' ).'>
                            <label for="google_picture">'.$language->translate('Google picture').'</label>';
                }
                ?>
            </div>

            <div class="sc-col sc-xs">
                <button class="sc-raised-button">
                    <i class="material-icons">save</i>
                    <?php echo $language->translate('Save'); ?>
                </button>
            </div>
        </form>
    </div>
<?php
	require_once $dash->getInclude('footer');
}