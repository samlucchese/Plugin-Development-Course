<?php 

// #35. create slt_register_user() function with 2 forms, one for 'create account', another for 'login' 
function slt_register_user(){
	
	// #37. check that the form has been submitted, then check the nonce is the same as the sl_translations_register_nonce registered in the form. nonce validation copied from /views/sl-translations_shortcode.
	if ( isset( $_POST['submitted'] ) ){
		if( isset( $_POST['sl_translations_register_nonce'] ) ) {
			if( ! wp_verify_nonce( $_POST['sl_translations_register_nonce'], 'sl_translations_register_nonce' ) ) {
				return;
			}
		}
		
		#40. Validate form fields with WE_Error() 1
		global $reg_errors;
		$reg_errors = new WP_Error();
		
		// #38. Get all form data and store it in variables 
		$username = sanitize_user( $_POST['username'] );
		$firstname = sanitize_text_field( $_POST['firstname'] );
		$lastname = sanitize_text_field( $_POST['lastname'] );
		$useremail = sanitize_email( $_POST['useremail'] );
		$password = $_POST['password'];
		
		#40. Validate form fields with WE_Error() 2
		if ( empty( $username ) || empty( $firstname ) || empty( $lastname ) || empty( $useremail ) || empty( $password ) ){
			$reg_errors->add( 'empty-field', esc_html__( 'Required form field is missing.', 'sl-translations' ) );
		}
		if ( strlen( $username ) < 6 ){
			$reg_errors->add( 'username_length', esc_html__( 'Username too short. At least 6 characters is required.', 'sl-translations' ) );
		}
		if ( username_exists( $username ) ){
			$reg_errors->add( 'user_name', esc_html__( 'Invalid credentials.', 'sl-translations' ) );
		}
		if ( ! validate_username( $username ) ){
			$reg_errors->add( 'username_invalid', esc_html__( 'The username you entered is not valid!', 'sl-translations' ) );
		}
		if ( ! is_email( $useremail ) ){
			$reg_errors->add( 'email_invalid', esc_html__( 'Email is not valid!', 'sl-translations' ) );
		}
		if ( email_exists( $useremail ) ){
			$reg_errors->add( 'email_exists', esc_html__( 'Email already exists.', 'sl-translations' ) );
		}
		if ( strlen( $password ) < 5 ){
			$reg_errors->add( 'password_length', esc_html__( 'Password length must be greater than 5.', 'sl-translations' ) );
		}
		
		// #41. Display any error messages
		if ( is_wp_error( $reg_errors ) ) {
			foreach ($reg_errors->get_error_messages() as $error) {
				?>
					<div style="color:#FF0000; text-align: left;">
						<?php echo $error;?>
					</div>
				<?php
			}
		}
		
		// --41a. Save the data to the table ONLY IF there are no errors
		if( count( $reg_errors->get_error_messages() ) < 1 ) {
			#39. Insert user data to wp_user table in the database
			$user_data = array(
				'user_login' => $username,
				'first_name' => $firstname,
				'last_name' => $lastname,
				'user_email' => $useremail,
				'user_pass' => $password,
				'role' => 'contributer'
			);
			$user = wp_insert_user( $user_data );
			wp_login_form();
		}
		
		
		
		
	} 
	// 41b. If the user is not set, show them the registration form
	if ( !isset( $user ) ) {
	?>
		<h3><?php esc_html_e( 'Create your account', 'sl-translations' ); ?></h3>
		<form action="" method="post" name="user_registeration">
			<label for="username"><?php esc_html_e( 'Username', 'sl-translations' ); ?> *</label>  
			<input type="text" name="username" required /><br />
			<label for="firstname"><?php esc_html_e( 'First Name', 'sl-translations' ); ?> *</label>  
			<input type="text" name="firstname" required /><br />
			<label for="lastname"><?php esc_html_e( 'Last Name', 'sl-translations' ); ?> *</label>  
			<input type="text" name="lastname" required /><br />
			<label for="useremail"><?php esc_html_e( 'Email address', 'sl-translations' ); ?> *</label>
			<input type="text" name="useremail" required /> <br />
			<label for="password"><?php esc_html_e( 'Password', 'sl-translations' ); ?> *</label>
			<input type="password" name="password" required /> <br />
			<input type="submit" name="user_registeration" value="<?php echo esc_attr__( 'Sign Up', 'sl-translations' ); ?>" />
		
			<input type="hidden" name="sl_translations_register_nonce" value="<?php echo wp_create_nonce( 'sl_translations_register_nonce' ); ?>">
			<input type="hidden" name="submitted" id="submitted" value="true" />
		</form>
		<h3><?php esc_html_e( 'Or login', 'sl-translations' ); ?></h3>
		<!-- Login form goes here... -->
		<?php wp_login_form(); ?>
	<?php
	}
}