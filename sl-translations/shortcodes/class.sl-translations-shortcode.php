<?php 
if ( !class_exists('SL_Translations_Shortcode') ) {
	class SL_Translations_Shortcode{
		function __construct(){
			add_shortcode('sl_translations', array($this, 'add_shortcode') );
		}
		
		public function add_shortcode(){	
			// Create and call a file for our HTML Markup. (Shortcode's view file)
			ob_start();
			require( SL_TRANSLATIONS_PATH . 'views/sl-translations_shortcode.php' );
			// #31. enqueue our scripts at the correct time 
			wp_enqueue_script('custom_js');
			wp_enqueue_script('validate_js');
			return ob_get_clean();
		}
	}
}