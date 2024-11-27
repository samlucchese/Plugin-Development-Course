<?php 

// 1. Create our post type class for SL Testimonials, then go back to main php file and instantiate (require_once() ) new class

if (!class_exists('SL_Testimonials_Post_Type')){
	class SL_Testimonials_Post_Type{
		
		// 2. create our constructor method
		public function __construct(){
			
			// 3. Call this method in the constructor
			add_action( 'init', array( $this, 'create_post_type' ) );
			
			// 5. add meta boxes hook. method down below
			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
			
			// 8. Save Post Hook 
			add_action( 'save_post', array( $this, 'save_post' ) );
			
		}
		
		// C. Create post type method + Call this method in the constructor
		public function create_post_type(){
			register_post_type(
				'sl-testimonials', 
				array(
					'label' => esc_html__( 'Testimonial', 'sl-testimonials' ),
					'description' => esc_html__( 'Testimonials', 'sl-testimonials'),
					'labels' => array(
						'name' =>  esc_html__( 'Testimonials', 'sl-testimonials'),
						'singular_name' =>  esc_html__( 'Testimonial', 'sl-testimonials'),
					),
					'public' => true,
					'supports' => array( 'title', 'editor', 'thumbnail'),
					'hierarchical' => false,
					'show_ui' => true,
					'show_in_menu' => true, // set this to true, 
					'menu_position' => 5,
					'show_in_admin_bar' => true,
					'show_in_nav_menus' => true,
					'can_export' => true,
					'has_archive' => true, // archive page with all testimonials
					'exclude_from_search' => false,
					'publicly_queryable' => true,
					'show_in_rest' => true,
					'menu_icon' => 'dashicons-testimonial',
					//'register_meta_box_cb' => array( $this, 'add_meta_boxes'),
				)
			);
		}
		
		// 6. add meta boxes method
		public function add_meta_boxes(){
			add_meta_box(
				'sl_testimonials_meta_box', // id 
				esc_html('Testimonials Options', 'sl-testimonials' ), //title, and plugins text-domain 
				array( $this, 'add_inner_meta_boxes'), //callback function for metabox content. callback down below
				'sl-testimonials',  //screen where custom meta box will appear. will only appear on an sl-testimonials post type
				'normal',
				'high'				
			);
		}
		
		// 7. add_inner_meta_boxes method
		public function add_inner_meta_boxes( $post ){
			require_once( SL_TESTIMONIALS_PATH . 'views/sl-testimonials_metabox.php' );
		}
		
		
		// 9. Save Post Method 
		public function save_post( $post_id ){
			// Guard clauses for the current custom post type only. Ensures that metadata from other plugins does not bleed to each new testimonial post.
			if (get_post_type($post_id) !== 'sl-testimonials') {
				return;
			}
			// Guard Clauses: protects the execution of the function 
			//  Check if the nonce set in the metabox html matches. More security measures.
			if( isset( $_POST['sl_testimonials_nonce'] ) ) {
				if( ! wp_verify_nonce( $_POST['sl_testimonials_nonce'], 'sl_testimonials_nonce' ) ) {
					return;
				}
			}
			
			// Verify that wordpress IS NOT autosaving the post.
			if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}
			
			//  Checks if the user has access to edit the post/page.
			if( isset( $_POST['post_type'] ) && $_POST['post_type'] === 'sl-testimonials'){
				if( ! current_user_can('edit_page', $post_id) ){
					return;
				}elseif( ! current_user_can('edit_post', $post_id ) ){
					return;
				}
			}
			
			if( isset( $_POST['action']) && $_POST['action'] == 'editpost'){
				$old_occupation = get_post_meta($post_id, 'sl_testimonials_occupation', true);
				$new_occupation = sanitize_text_field( $_POST['sl_testimonials_occupation'] );
				$old_company = get_post_meta($post_id, 'sl_testimonials_company', true);
				$new_company = sanitize_text_field( $_POST['sl_testimonials_company'] );
				$old_user_url = get_post_meta($post_id, 'sl_testimonials_user_url', true);
				$new_user_url = esc_url_raw( $_POST['sl_testimonials_user_url'] );
				
				// Updates the data entered to the metabox fields to the metadata database table. should appear in wp_postmeta table.
				update_post_meta( $post_id, 'sl_testimonials_occupation', $new_occupation, $old_occupation );
				update_post_meta( $post_id, 'sl_testimonials_company', $new_company, $old_company );
				update_post_meta( $post_id, 'sl_testimonials_user_url', $new_user_url, $old_user_url );
			}
			
		}
		
		
	}
}