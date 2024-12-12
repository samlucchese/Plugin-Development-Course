<!-- #45b. Create /views/sl-translations_edit_shortcode.php. Copied code from /views/sl-translations_shortcode -->
<!-- See original code comments in /views/sl-translations_shortcode.php -->


<?php  
if ( ! is_user_logged_in() ) {
	slt_register_user();
	return;
}

if( isset( $_POST['sl_translations_nonce'] ) ) {
	if( ! wp_verify_nonce( $_POST['sl_translations_nonce'], 'sl_translations_nonce' ) ) {
		return;
	}
}

$errors = array();
$hasError = false;

if( isset( $_POST['submitted'])){
	$title				= $_POST['sl_translations_title'];
	$content            = $_POST['sl_translations_content'];
	$singer             = $_POST['sl_translations_singer'];
	$transliteration    = $_POST['sl_translations_transliteration'];
	$video              = $_POST['sl_translations_video_url'];
	
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
	
	
	if ($hasError === false) { 
		$post_info = array(
			'post_type' => 'sl-translations',
			'post_title' => sanitize_text_field( $title ), 
			'post_content' => wp_kses_post( $content ),  
			'tax_input' => array(
				'singers' => sanitize_text_field($singer) 
			),
			// We can comment out this line below
			// 'post_status' => 'pending
			
			// #46. ADD POST ID so that wordpress knows the ID of the post to edit.
			'ID' => $_GET['post']
		);
		
		// Changed from wp_insert_post() to wp_update_post(); in EDIT file  
		$post_id = wp_update_post( $post_info );
		
		global $post;
		
		//go down to our hidden sl_translations_action <input> and change value="save" to value="update"
		SL_Translations_Post_Type::save_post( $post_id, $post ); 
	}
	
}



global $current_user;
global $wpdb;

// SELECT clause:	removed post_date, post_status, added post_content 
// WHERE clause:	needs 2 parameters, post ID
// removed the below two ANDs
// AND tm.meta_key = 'sl_translations_transliteration'
// AND p.post_status IN ('publish', 'pending')
$q = $wpdb->prepare(
	"SELECT ID, post_author, post_title, post_content, meta_key, meta_value
	FROM $wpdb->posts AS p
	INNER JOIN $wpdb->translationmeta AS tm
	ON p.ID = tm.translation_id
	WHERE p.ID = %d
	AND p.post_author = %d
	ORDER BY p.post_date DESC",
	$_GET['post'],
	$current_user->ID
);
// Adding another parameter to the get_results() function, ARRAY_A. We want the output of get_results() to be an array.
$results = $wpdb->get_results( $q, ARRAY_A );
var_dump( $results );
?>



<div class="sl-translations">
	<form action="" method="POST" id="translations-form">
		<h2><?php esc_html_e( 'Edit translation' , 'sl-translations' ); ?></h2>
		
		<?php 
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
		
		<!-- #48. Show the existing values already populating the fields so we can correctly 'edit' the inputs -->
		<label for="sl_translations_title"><?php esc_html_e( 'Title', 'sl-translations' ); ?> *</label>
		<!-- #48a. Remove if() from field <input>'s' -->
		<input type="text" name="sl_translations_title" id="sl_translations_title" value="<?php echo esc_html( $results[0]['post_title'] ); ?>" required />
		<br />
		<label for="sl_translations_singer"><?php esc_html_e( 'Singer', 'sl-translations' ); ?> *</label>
		<!-- #48b. Singer field is a Taxonomy, handled a bit differently. -->
		<input type="text" name="sl_translations_singer" id="sl_translations_singer" value="<?php echo strip_tags(get_the_term_list( $_GET['post'], 'singers', '', ', ' ) ); ?>" required />

		<br />
		<?php 
			// #48c. Populates the editor field
			wp_editor($results[0]['post_content'], 'sl_translations_content', array( 'wpautop' => true, 'media_buttons' => false ) );
		?>
		</br />
		
		<fieldset id="additional-fields">
			<label for="sl_translations_transliteration"><?php esc_html_e( 'Has transliteration?', 'sl-translations' ); ?></label>
			<select name="sl_translations_transliteration" id="sl_translations_transliteration">
				<option value="Yes" <?php if (isset($transliteration)) selected( $transliteration, "Yes" ); ?>><?php esc_html_e( 'Yes', 'sl-translations' ); ?></option>
				<option value="No" <?php if (isset($transliteration)) selected( $transliteration, "No" ); ?>><?php esc_html_e( 'No', 'sl-translations' ); ?></option>
			</select>
			<label for="sl_translations_video_url"><?php esc_html_e( 'Video URL', 'sl-translations' ); ?></label>
			<input type="url" name="sl_translations_video_url" id="sl_translations_video_url" value="<?php if( isset( $video ) ) echo $video; ?>" />
		</fieldset>
		<br />
		<!-- value="saved" below is changed to value="update" -->
		<input type="hidden" name="sl_translations_action" value="update">
		<input type="hidden" name="action" value="editpost">
		<input type="hidden" name="sl_translations_nonce" value="<?php echo wp_create_nonce( 'sl_translations_nonce' ); ?>">
		<input type="hidden" name="submitted" id="submitted" value="true" />
		<input type="submit" name="submit_form" value="<?php esc_attr_e( 'Submit', 'sl-translations' ); ?>" />
	</form>
</div>

