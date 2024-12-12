<!-- #36. Test user registration by calling slt_register_user()  -->
<?php  
if ( ! is_user_logged_in() ) {
	slt_register_user();
	return;
}
?>


<!-- #24. Validate the form submission. -->
<!-- ---- This is standard practice for if the post is submitted and the superglobal is populated. The cue to start processing the form. -->
<?php

// #25b. Security check on the post_type data. Copied from class.sl-translations-cpt.php in the save_post() method. If the nonce doesnt match, do not even continue.
if( isset( $_POST['sl_translations_nonce'] ) ) {
	if( ! wp_verify_nonce( $_POST['sl_translations_nonce'], 'sl_translations_nonce' ) ) {
		return;
	}
}

// #26a. Create Variables for validation errors
$errors = array();
$hasError = false;

if( isset( $_POST['submitted'])){
	$title				= $_POST['sl_translations_title'];
	$content            = $_POST['sl_translations_content'];
	$singer             = $_POST['sl_translations_singer'];
	$transliteration    = $_POST['sl_translations_transliteration'];
	$video              = $_POST['sl_translations_video_url'];
	
	// #26b. Validate submitted variables: 
	// If, when we trim the variable, it is empty, errors will populate the array. 
	// (trim removes all empty space from the input)
	if ( trim( $title ) === '' ){
		$errors[] = esc_html__('Please, enter a title.', 'sl-translations' );
		$hasError = true;
	}
	if ( trim( $content ) === '' ){
		$errors[] = esc_html__('Please, enter some content.', 'sl-translations' );
		$hasError = true;
	}
	if ( trim( $singer ) === '' ){
		$errors[] = esc_html__('Please, enter a singer.', 'sl-translations' );
		$hasError = true;
	}
	
	
	if ($hasError === false) { // Send information to the database only if we don't have any errors 
		
		// submit the first 3 values to our wp_posts table (using wp_insert_post), and the last 2 to our custom table.
		// first create an array of arguments to be used inside wp_insert_post() 
		$post_info = array(
			'post_type' => 'sl-translations',
			'post_title' => sanitize_text_field( $title ), //#25a. Sanitize $post_info values
			'post_content' => wp_kses_post( $content ),  //#25a. Sanitize $post_info values. This wp_kses_post() sanitization is used exclusively to sanitize a value passed to the post editor.
			'tax_input' => array(
				'singers' => sanitize_text_field($singer) //#25a. Sanitize $post_info values
			),
			'post_status' => 'pending',
		);
		
		// call wp_insert_post() to submit the table with the $post_info. This returns the id of the post that will be saved. This is why we 'hijack' the id by assigning $post_id to it. this line essentially gets the ID of the new post and assigns it to $post_id.
		$post_id = wp_insert_post( $post_info );
		
		global $post; // wordpress global object, in save_post() we use global $wpdb; We dont have it in this file so we define the $post variable here.
		
		// call the save_post method on the SL_Translations_Post_Type
		SL_Translations_Post_Type::save_post( $post_id, $post ); // Here I went and set the save_post() method in /post-types/class.sl-translations-cpt.php to STATIC so that it can be used here 
	}
	
}
?>


<!-- #22a. Create the HTML form for submitting translation information -->
<div class="sl-translations">
	<form action="" method="POST" id="translations-form">
		<h2><?php esc_html_e( 'Submit new translation' , 'sl-translations' ); ?></h2>
		
		<?php 
			// #27. Show errors at the start of the form
			// if the errors are not empty, iterate through each error
			if ( $errors != ''){
				foreach( $errors as $error ){ 
					?>
					<span class="error">
						<?php echo $error; ?>
					</span>
					<?php 
				}
			}
		?>
		
		<label for="sl_translations_title"><?php esc_html_e( 'Title', 'sl-translations' ); ?> *</label>
		<!-- #33. Ensure values entered will stay entered even on form submission. Edit the value="" attribute  -->
		<input type="text" name="sl_translations_title" id="sl_translations_title" value="<?php if( isset( $title ) ) echo $title; ?>" required />
		<br />
		<label for="sl_translations_singer"><?php esc_html_e( 'Singer', 'sl-translations' ); ?> *</label>
		<input type="text" name="sl_translations_singer" id="sl_translations_singer" value="<?php if( isset( $singer ) ) echo $singer; ?>" required />

		<br />
		<?php 
		// #33. Ensure Values entered will stay entered even on form submission by adding this if/else statement 
		if ( isset( $content ) ){
			// #33a. change first empty argument from else statement to $content in wp_editor() function 
			wp_editor($content, 'sl_translations_content', array( 'wpautop' => true, 'media_buttons' => false ) );
		}else{
			// #22b. Use wp_editor() to add a WYSIWYG editor to the HTML Markup
			wp_editor('', 'sl_translations_content', array( 'wpautop' => true, 'media_buttons' => false ) );
		}
		
		?>
		</br />
		
		<fieldset id="additional-fields">
			<label for="sl_translations_transliteration"><?php esc_html_e( 'Has transliteration?', 'sl-translations' ); ?></label>
			<select name="sl_translations_transliteration" id="sl_translations_transliteration">
				<!-- #33b. For Yes or No fields, we can use the selected() function. -->
				<option value="Yes" <?php if (isset($transliteration)) selected( $transliteration, "Yes" ); ?>><?php esc_html_e( 'Yes', 'sl-translations' ); ?></option>
				<option value="No" <?php if (isset($transliteration)) selected( $transliteration, "No" ); ?>><?php esc_html_e( 'No', 'sl-translations' ); ?></option>
			</select>
			<label for="sl_translations_video_url"><?php esc_html_e( 'Video URL', 'sl-translations' ); ?></label>
			<input type="url" name="sl_translations_video_url" id="sl_translations_video_url" value="<?php if( isset( $video ) ) echo $video; ?>" />
		</fieldset>
		<br />
		<input type="hidden" name="sl_translations_action" value="save">
		<input type="hidden" name="action" value="editpost">
		<input type="hidden" name="sl_translations_nonce" value="<?php echo wp_create_nonce( 'sl_translations_nonce' ); ?>">
		<input type="hidden" name="submitted" id="submitted" value="true" />
		<input type="submit" name="submit_form" value="<?php esc_attr_e( 'Submit', 'sl-translations' ); ?>" />
	</form>
</div>

<!-- start of the table output -->
<div class="translations-list">
			<table>
				<caption><?php esc_html_e( 'Your Translations', 'sl-translations' ); ?></caption>
				<thead>
					<tr>
						<th><?php esc_html_e( 'Date', 'sl-translations' ); ?></th>
						<th><?php esc_html_e( 'Title', 'sl-translations' ); ?></th>
						<th><?php esc_html_e( 'Transliteration', 'sl-translations' ); ?></th>
						<th><?php esc_html_e( 'Edit?', 'sl-translations' ); ?></th>
						<th><?php esc_html_e( 'Delete?', 'sl-translations' ); ?></th>
						<th><?php esc_html_e( 'Status', 'sl-translations' ); ?></th>
					</tr>
				</thead>  
				<tbody>  
					<tr>
						<td>Date</td>
						<td>Title</td>
						<td>Transliteraton</td>
						<td>Edit</td>
						<td>Delete</td>
						<td>Status</td>
					</tr>
			</tbody>
		</table>
</div>