<?php 
if ( !class_exists( 'SL_Slider_Post_Type' ) ){
	class SL_Slider_Post_Type{
		function __construct(){
			// Add Actions, calling methods below.
			// 1. Init + Create Post Type 
			add_action( 'init', array($this, 'create_post_type') );
			
			// 2. Add meta boxes fields
			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes') );
			
			// 3. Save data entered to metaboxes
			add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
			
			// 5. Add columns to admin panel in the backend
			add_filter( 'manage_sl-slider_posts_columns', array( $this, 'sl_slider_cpt_columns') );
			// 5a. Adds the VALUES to the newly added columns.
			add_action( 'manage_sl-slider_posts_custom_column', array( $this, 'sl_slider_custom_columns'), 10, 2 );
			// 5b. Allow sorting on the newly created columns in the admin panel.
			add_filter( 'manage_edit-sl-slider_sortable_columns', array($this, 'sl_slider_sortable_columns') );
		}
		
		// Methods for add_action()'s above
		//1. Init + Create Post Type 
		public function create_post_type(){
			register_post_type(
				'sl-slider', 
				array(
					'label' => 'Slider',
					'description' => 'Sliders',
					'labels' => array(
						'name' => 'Sliders',
						'singular_name' => 'Slider',
						'add_new' => 'Add New Slider',
						'add_new_item' => 'Add New Slider',
					),
					'public' => true,
					'supports' => array( 'title', 'editor', 'thumbnail'),
					'hierarchical' => false,
					'show_ui' => true,
					'show_in_menu' => false,
					'menu_position' => 5,
					'show_in_admin_bar' => true,
					'show_in_nav_menus' => true,
					'can_export' => true,
					'has_archive' => false,
					'exclude_from_search' => false,
					'publicly_queryable' => true,
					'show_in_rest' => true,
					'menu_icon' => 'dashicons-images-alt2',
					//'register_meta_box_cb' => array( $this, 'add_meta_boxes'),
				)
			);
			
		}
		// 5a. Adds the VALUES to the newly added columns.
		public function sl_slider_custom_columns( $column, $post_id){
			switch( $column ){
				case 'sl_slider_link_text':
					echo esc_html( get_post_meta( $post_id, 'sl_slider_link_text', true ) );
				break;
				case 'sl_slider_link_url':
					echo esc_url( get_post_meta( $post_id, 'sl_slider_link_url', true ) );
				break;
			}
		}
		
		
		// 5b. Allow sorting on the newly created columns in the admin panel.
		public function sl_slider_sortable_columns( $columns ){
			$columns['sl_slider_link_text'] = 'sl_slider_link_text';
			$columns['sl_slider_link_url'] = 'sl_slider_link_url';
			return $columns;
		}
		
		// 2. Add meta boxes fields
		public function add_meta_boxes(){
			add_meta_box(
				'sl_slider_meta_box',
				'Link Options',
				array( $this, 'add_inner_meta_boxes'),
				'sl-slider',
				'normal',
				'high',
			); 
		}
		
		// 2a. Adds fields set in 'views/sl-slider_metabox.php' to the metabox
		public function add_inner_meta_boxes( $post ){
			require_once( SL_SLIDER_PATH . 'views/sl-slider_metabox.php');
		}
		
		
		// 5. Method to add columns to admin panel in the backend
		public function sl_slider_cpt_columns( $columns ) {
			$columns['sl_slider_link_text'] = esc_html__( 'Link Text', 'sl-slider');
			$columns['sl_slider_link_url'] = esc_html__( 'Link URL', 'sl-slider');
			return $columns;
		}
		
		
		// 3. Save data entered to metaboxes
		public function save_post( $post_id ){
			
			// 4. Guard Clauses: protects the execution of the function 
			// 4a. Check if the nonce set in the metabox html matches. More security measures.
			if( isset( $_POST['sl_slider_nonce'] ) ) {
				if( ! wp_verify_nonce( $_POST['sl_slider_nonce'], 'sl_slider_nonce' ) ) {
					return;
				}
			}
			// 4b. Verify that wordpress IS NOT autosaving the post.
			if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}
			// 4c. Checks if the user has access to edit the post/page.
			if( isset( $_POST['post_type'] ) && $_POST['post_type'] === 'sl-slider'){
				if( ! current_user_can('edit_page', $post_id) ){
					return;
				}elseif( ! current_user_can('edit_post', $post_id ) ){
					return;
				}
			}
			
			if( isset( $_POST['action']) && $_POST['action'] == 'editpost'){
				$old_link_text = get_post_meta($post_id, 'sl_slider_link_text', true);
				$new_link_text = sanitize_text_field( $_POST['sl_slider_link_text'] );
				$old_link_url = get_post_meta($post_id, 'sl_slider_link_url', true);
				$new_link_url = esc_url_raw( $_POST['sl_slider_link_url'] );
				
				// 3a. Updates the data entered to the metabox fields to the metadata database table. should appear in wp_postmeta table.
				// -- add checks so that if it is empty some input is filled automatically
				if (empty( $new_link_text )){
					update_post_meta( $post_id, 'sl_slider_link_text', 'Add some text' );
				}else{
					update_post_meta( $post_id, 'sl_slider_link_text', $new_link_text, $old_link_text );
				}
				if (empty( $new_link_url )){
					update_post_meta( $post_id, 'sl_slider_link_url', '#' );
				}else{
					update_post_meta( $post_id, 'sl_slider_link_url', $new_link_url, $old_link_url );
				}
			}
		}
	}
}
