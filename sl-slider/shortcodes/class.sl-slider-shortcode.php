<?php 
if ( !class_exists('SL_Slider_Shortcode') ) {
	class SL_Slider_Shortcode{
		function __construct(){
			add_shortcode('sl_slider', array($this, 'add_shortcode') );
		}
		
		public function add_shortcode( $atts = array(), $content = null, $tag = '' ){
			
			$atts = array_change_key_case( (array) $atts, CASE_LOWER );
			
			extract( shortcode_atts(
				array(
					'id' => '',
					'orderby' => 'date',
				),
				$atts,
				$tag
			));
			
			// Ensures the value entered is not empty, and ONLY an int
			if( !empty( $id ) ) {
				$id = array_map( 'absint', explode(',', $id ) );
			}
			
			// Create and call a file for our HTML Markup. (Shortcode's view file)
			ob_start();
			require( SL_SLIDER_PATH . 'views/sl-slider_shortcode.php' );
			// Enqueue styles/scripts only when this plugin is called.
			wp_enqueue_script( 'sl-slider-main-jq' );
			// deleted below line when we added to functions.php
			// wp_enqueue_script( 'sl-slider-options-js' );
			wp_enqueue_style( 'sl-slider-main-css' );
			wp_enqueue_style( 'sl-slider-frontend-css' );
			sl_slider_options();
			return ob_get_clean();
		}
	}
}