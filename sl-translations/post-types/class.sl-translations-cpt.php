<?php 
// #3. create the file for the plugin CPT
if ( ! class_exists( 'SL_Translations_Post_Type' ) ){
	class SL_Translations_Post_Type{
		public function __construct(){
			
			
			// #3a. create_post_type hook
			add_action( 'init', array( $this, 'create_post_type' ) );
			
			// #5a. create_taxonomy hook
			add_action( 'init', array( $this, 'create_taxonomy' ) );
			
			// #7a. register_metadata_table hook
			add_action( 'init', array( $this, 'register_metadata_table' ) );
			
			// #8a. add_meta_boxes hook
			add_action( 'add_meta_boxes', array($this, 'add_meta_boxes') );
			
			// #12a. wp_insert_post hook with save_post callback. save_post callback method down below.
			add_action( 'wp_insert_post', array($this, 'save_post'), 10, 2 );
			
			// # 19a. delete_post hook 
			add_action( 'delete_post', array( $this , 'delete_post' ));
			
			// #54a. pre_get_posts/add_cpt_author hook, method down below.
			add_action( 'pre_get_posts', array( $this, 'add_cpt_author' ) );
		}
		
		// #3b. create_post_type method
		public function create_post_type(){
			//taken from a previous plugin create_post_type() function 
			register_post_type(
				'sl-translations', 
				array(
					'label' => esc_html__( 'Translation', 'sl-translations' ),
					'description' => esc_html__( 'Translations', 'sl-translations'),
					'labels' => array(
						'name' =>  esc_html__( 'Translations', 'sl-translations'),
						'singular_name' =>  esc_html__( 'Translation', 'sl-translations'),
						'add_new' =>  esc_html__( 'Add New Translation', 'sl-translations'),
						'add_new_item' =>  esc_html__( 'Add New Translation', 'sl-translations'),
					),
					'public' => true,
					'supports' => array( 'title', 'editor', 'author'),
					'rewrite' => array( 'slug' => 'translations' ), // rewrites the slug to a more friendly term
					'hierarchical' => false,
					'show_ui' => true,
					'show_in_menu' => true, // set this to true, 
					'menu_position' => 5,
					'show_in_admin_bar' => true,
					'show_in_nav_menus' => true,
					'can_export' => true,
					'has_archive' => true, // archive page with all translations
					'exclude_from_search' => false,
					'publicly_queryable' => true,
					'show_in_rest' => true,
					'menu_icon' => 'dashicons-admin-site',
					//'register_meta_box_cb' => array( $this, 'add_meta_boxes'),
				)
			);
		}
		
		// #5b. create_taxonomy method
		public function create_taxonomy(){
			register_taxonomy(
				'singers',
				'sl-translations',
				array(
					'labels' =>array(
						'name' => __( 'Singers', 'sl-translations' ),
						'singular_name' => __( 'Singer', 'sl-translations' ),
					),
					'hierarchical' => false,
					'show_in_rest' => true,
					'public' => true,
					'show_admin_column' => true,
					'show_ui' => true,
				)
			);
		} 
		// #54b. pre_get_posts/add_cpt_author method 
		public function add_cpt_author( $query ){
			if ( !is_admin() && $query->is_author() && $query->is_main_query() ){
				$query->set( 'post_type', array( 'sl-translations', 'post' ) );
			}
		}
		
		// #7b. register_metadata_table method
		public function register_metadata_table(){
			global $wpdb; 
			$wpdb->translationmeta = $wpdb->prefix . 'translationmeta'; // saves time by shortening the name for the database
		}
		
		// #8b. add_meta_boxes Method
		public function add_meta_boxes(){
			add_meta_box(
				'sl_translations_meta_box', // id 
				esc_html__('Translations Options', 'sl-translations' ), //title, and plugins text-domain 
				array( $this, 'add_inner_meta_boxes'), //callback function for metabox content. callback down below
				'sl-translations',  //screen where custom meta box will appear. will only appear on an sl-translations post type
				'normal',
				'high'				
			);
		}
		// #7. add_inner_meta_boxes method
		public function add_inner_meta_boxes( $post ){
			require_once( SL_TRANSLATIONS_PATH . 'views/sl-translations_metabox.php' );
		}
		
		// #12b. save_post callback method for wp_insert_post() hook
		// --- static in the method allos us to use this save_post method outside of this file, for example, in the /views/sl-translations_shortcode.php file 
		public static function save_post($post_id, $post){
			// Guard clauses for the current custom post type only. Ensures that metadata from other plugins does not bleed to each new Translation post.
			if (get_post_type($post_id) !== 'sl-translations') {
				return;
			}
			// 	---------------- Guard Clauses: protects the execution of the function  ----------------------
			//  Check if the nonce set in the metabox html matches. More security measures.
			if( isset( $_POST['sl_translations_nonce'] ) ) {
				if( ! wp_verify_nonce( $_POST['sl_translations_nonce'], 'sl_translations_nonce' ) ) {
					return;
				}
			}
			
			// Verify that wordpress IS NOT autosaving the post.
			if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}
			
			//  Checks if the user has access to edit the post/page.
			if( isset( $_POST['post_type'] ) && $_POST['post_type'] === 'sl-translations'){
				if( ! current_user_can('edit_page', $post_id) ){
					return;
				}elseif( ! current_user_can('edit_post', $post_id ) ){
					return;
				}
			}
			// ----------------- End guard clauses ----------------------
			
			// keeps record of the post data in case the post is deleted and then restored
			// #13. Create 2 variables, each one for each field in the metabox 
			if( isset( $_POST['action']) && $_POST['action'] == 'editpost'){
				
				$transliteration = sanitize_text_field( $_POST['sl_translations_transliteration'] );
				$video = esc_url_raw( $_POST['sl_translations_video_url'] );
				
				global $wpdb;
				// #17. Wrap validation + insert method in the 'save' if statement 
				if( $_POST['sl_translations_action'] == 'save') {
					// before insert method we need some validation  
					// post status: make sure the post is published + exists (using get_var)
					if( get_post_type( $post ) == 'sl-translations' && 
						$post->post_ststus != 'trash' && 
						$post->post_status != 'auto-draft' && 
						$post->post_status != 'draft' && 
						$wpdb->get_var(
							$wpdb->prepare(
								"SELECT translation_id
								FROM $wpdb->translationmeta
								WHERE translation_id = %d",
								$post_id
							)) == null
					){
						// #15. insert method for transliteration field
						$wpdb->insert(
							$wpdb->translationmeta, //table name
							array(
								'translation_id' => $post_id, 
								'meta_key' => 'sl_translations_transliteration',
								'meta_value' => $transliteration
							), //array containing the data we want to pass to the table (in key and value format)
							array(
								'%d', '%s', '%s'
							) // array with the format of all fields we want to pass to the table 
						);
						// #15. insert method for video_url field 
						$wpdb->insert(
							$wpdb->translationmeta, //table name
							array(
								'translation_id' => $post_id, 
								'meta_key' => 'sl_translations_video_url',
								'meta_value' => $video
							), //array containing the data we want to pass to the table (in key and value format)
							array(
								'%d', '%s', '%s'
							) // array with the format of all fields we want to pass to the table 
						);
					}
				} else{
					if( get_post_type( $post ) == 'sl-translations' ){
						// 18. Add $wpdb->update() method in the else statement for transliteration
						$wpdb->update(
							$wpdb->translationmeta, //table name
							array(
								'meta_value' => $transliteration
							), // VALUE array with the columns we want to update with the new values 
							array(
								'translation_id' => $post_id, 
								'meta_key' => 'sl_translations_transliteration',
							), // WHERE array equivalent to the WHERE command in an SQL statement
							// two more optional parameters,
							array( '%s' ), // an array to format the VALUEs
							array( '%d', '%s' ), // an array to format data in the WHERE array 
						);
						// 18. Add $wpdb->update() method in the else statement for video_url 
						$wpdb->update(
							$wpdb->translationmeta, //table name
							array(
								'meta_value' => $video
							), // VALUE array with the columns we want to update with the new values 
							array(
								'translation_id' => $post_id, 
								'meta_key' => 'sl_translations_video_url',
							), // WHERE array equivalent to the WHERE command in an SQL statement
							// two more optional parameters,
							array( '%s' ), // an array to format the VALUEs
							array( '%d', '%s' ), // an array to format data in the WHERE array 
						);
					}
				}
			}
			
		}
		# 19b. delete_post method
		public function delete_post( $post_id ){
			if ( ! current_user_can('delete_posts') ) {
				return;
			}
			if( get_post_type( $post_id ) == 'sl-translations' ){
				global $wpdb;
				// $wpdb->delete() is similar to $wpdb->update()
				$wpdb->delete(
					$wpdb->translationmeta, // table name
					array( 'translation_id' => $post_id), // Array with value from the WHERE clause
					array( '%d' )//array with the format of the fields in the WHERE clause
				);
			}
		}
	}
}