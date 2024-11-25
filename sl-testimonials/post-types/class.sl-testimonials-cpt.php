<?php 
// 1. Create our post type class for SL Testimonials, then go back to main php file and instantiate (require_once() ) new class
if (!class_exists('SL_Testimonials_Post_Type')){
	class SL_Testimonials_Post_Type{
		// 1a. create our constructor method
		public function __construct(){
			add_action( 'init', array( $this, 'create_post_type' ) );
			
			// 2. add meta boxes hook. method down below
			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
			
		}
		
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
		
		// 2.1 add meta boxes method
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
		
		// 2.2. add_inner_meta_boxes method
		public function add_inner_meta_boxes( $post ){
			require_once( SL_TESTIMONIALS_PATH . 'views/sl-testimonials_metabox.php' );
		}
		
		
	}
}