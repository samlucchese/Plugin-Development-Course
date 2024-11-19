<?php 
//this function relocates the placeholder image sp it isnt in the sl-sider_shortcode.php file.
if ( !function_exists( 'sl_slider_get_placeholder_image' )){
	function sl_slider_get_placeholder_image(){
		return "<img src='" . SL_SLIDER_URL . "assets/images/default.jpg' class='img-fluid wp-post-image' />";
	}
}

// This is the file that allows us to use PHP values in js files. (sl_slider_options()) We would not have been able to use PHP in flexslider.js to change the controlNav value to show/hide the bullets depending on what was set on the Plugin Options admin page. This allows us to do that. 


if ( !function_exists( 'sl_slider_options' )){
	function sl_slider_options(){
		
		// if the value of the field in the wp_options table in the 'sl_slider_bullets' key is set, and the value is 1 (if the checkbox is checked), then set $show_bullets to true. Else set it to false.
		$show_bullets = isset( SL_Slider_Settings::$options['sl_slider_bullets']) && SL_Slider_Settings::$options['sl_slider_bullets'] == 1 ? true : false;	
		
		// Register Flexslider JS file
		// -- moved from sl-slider.php, changed from register to enqueue. Localizing script underneath.
		wp_enqueue_script(
			'sl-slider-options-js',
			SL_SLIDER_URL . 'vendor/flexslider/flexslider.js',
			array( 'jquery' ),
			SL_SLIDER_VERSION,
			true
		);
		
		wp_localize_script(
			'sl-slider-options-js', 
			'SLIDER_OPTIONS', 
			array(
			'controlNav' => $show_bullets
			) 
		);
	}
}
