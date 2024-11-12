<?php 

/**
* Plugin Name: SL Slider
* Plugin URI: https://www.wordpress.org/sl-slider
* Description: My plugin's description
* Version: 1.0
* Requires at least: 5.6
* Author: Sam Lucchese
* Author URI: https://authorsitename.com
* License: GPL v2 or later
* License URI: https://www.gnu.org/licenses/gpl-2.0.html
* Test Domain: sl-slider
* Domain Path: /languages
*/

/*
SL Slider is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

SL Slider is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with SL Slider. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/

if ( ! defined( 'ABSPATH' ) ){
	exit;
}

// If SL Slider class exists elsewhere in the project, say functions.php, use that.
// -- If SL Slider class does not exist elsewhere, use what is defined below.
if ( !class_exists( 'SL_Slider' ) ) {
	class SL_Slider{
		function __construct(){
			$this->define_constants();
			
			require_once( SL_SLIDER_PATH . 'post-types/class.sl-slider-cpt.php');
			$SL_Slider_Post_Type = new SL_Slider_Post_Type();
		}
		
		// Here we define constants like URL paths that we will use frequently during the build.
		public function define_constants(){
			// Gets the PATH, not the URL.
			define( 'SL_SLIDER_PATH', plugin_dir_path(__FILE__));
			// Gets the URL, not the PATH.
			define( 'SL_SLIDER_URL', plugin_dir_url(__FILE__));
			// Stylesheets get the URL, not the PATH.
			
			define( 'SL_SLIDER_VERSION', '1.0.0' );
		}
		
		// this function activates and creates the slides for the slideshow. Flushes and rewrites permalinks
		public static function activate() {
			update_option('rewrite_rules', '');
		}
		
		public static function deactivate() {
			flush_rewrite_rules();
			unregister_post_type( 'sl-slider' );
		}
		
		public static function uninstall() {
			
		}
		
	}
}

if ( class_exists( 'SL_Slider' ) ) {
	register_activation_hook( __FILE__, array( 'SL_Slider', 'activate') );
	register_deactivation_hook( __FILE__, array( 'SL_Slider', 'deactivate') );
	register_uninstall_hook( __FILE__, array( 'SL_Slider', 'uninstall') );
	
	$sl_slider = new SL_Slider();
}
