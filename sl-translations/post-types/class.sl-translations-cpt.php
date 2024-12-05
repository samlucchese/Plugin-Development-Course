<?php 
// 3. create the file for the plugin CPT
if ( ! class_exists( 'SL_Translations_Post_Type' ) ){
	class SL_Translations_Post_Type{
		public function __construct(){
			
			// 3a. create_post_type hook
			add_action( 'init', array( $this, 'create_post_type' ) );
			
			// 5a. create_taxonomy hook
			add_action( 'init', array( $this, 'create_taxonomy' ) );
			
			// 7a. register_metadata_table hook
			add_action( 'init', array( $this, 'register_metadata_table' ) );
			
			// 8a. add_meta_boxes hook
			add_action( 'add_meta_boxes', array($this, 'add_meta_boxes') );
			
			// 12a. wp_insert_post hook with save_post callback. save_post callback method down below.
			add_action( 'wp_insert_post', array($this, 'save_post'), 10, 2 );
			
		}
		
		// 3b. create_post_type method
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
		
		// 5b. create_taxonomy method
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
		
		// 7b. register_metadata_table method
		public function register_metadata_table(){
			global $wpdb; 
			$wpdb->translationmeta = $wpdb->prefix . 'translationmeta'; // saves time by shortening the name for the database
		}
		
		// 8b. add_meta_boxes Method
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
		// 7. add_inner_meta_boxes method
		public function add_inner_meta_boxes( $post ){
			require_once( SL_TRANSLATIONS_PATH . 'views/sl-translations_metabox.php' );
		}
		
		// 12b. save_post callback method for wp_insert_post() hook
		public function save_post($post_id, $post){
			// Guard clauses for the current custom post type only. Ensures that metadata from other plugins does not bleed to each new Translation post.
			if (get_post_type($post_id) !== 'sl-translations') {
				return;
			}
			// Guard Clauses: protects the execution of the function 
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
			
			// keeps record of the post data in case the post is deleted and then restored
			// 13. Create 2 variables, each one for each field in the metabox 
			if( isset( $_POST['action']) && $_POST['action'] == 'editpost'){
				
			}
			
		}
	}
}