<!-- #45a. Create shortcode file for new EDIT shortcode (/shortcodes/class.sl-translations-edit-shortcode.php) -->

<?php 
if ( !class_exists('SL_Translations_Edit_Shortcode') ) {
	class SL_Translations_Edit_Shortcode{
		function __construct(){
			// add_shortcode('name_of_shortcode', callback array());
			add_shortcode('sl_translations_edit', array($this, 'add_shortcode') );
		}
		
		public function add_shortcode(){	
			// Create and call a file for our HTML Markup. (Shortcode's view file)
			ob_start();
			require( SL_TRANSLATIONS_PATH . 'views/sl-translations_edit_shortcode.php' );
			// #45b. (leave this alone from the first shortcode file) enqueue our scripts at the correct time 
			wp_enqueue_script('custom_js');
			wp_enqueue_script('validate_js');
			return ob_get_clean();
		}
	}
}