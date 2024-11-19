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
			
			// require once Functions.php file for script localization 
			require_once( SL_SLIDER_PATH . 'functions/functions.php' );
			
			// Add an admin menu for plugin. Function down below. 
			add_action( 'admin_menu', array( $this, 'add_menu') );
			
			// require custom post type class file + instantiate the class object
			require_once( SL_SLIDER_PATH . 'post-types/class.sl-slider-cpt.php');
			$SL_Slider_Post_Type = new SL_Slider_Post_Type();
			
			// require custom settings page file + instantiate the class object
			require_once( SL_SLIDER_PATH . 'class.sl-slider-settings.php');
			$SL_Slider_Settings = new SL_Slider_Settings();
			
			//After adding shortcode class file,  require new shortcode file 
			require_once( SL_SLIDER_PATH . '/shortcodes/class.sl-slider-shortcode.php');
			//instantiate the class as well.
			$SL_Slider_Shortcode = new SL_Slider_Shortcode();
			
			//adding/enqueueing files/scripts for flexslider resources. Function down below
			add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ), 999 ); 
			
			//enqueue admin scripts for styles in admin dash. (Metabox styling, styling the editor, etc.). Function down below.
			add_action( 'admin_enqueue_scripts', array($this, 'register_admin_scripts') );
			
			
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
		
		
		// Method to add an admin menu for plugin. add_action() called above.
		public function add_menu(){
			
			add_menu_page( //adds the menu page to the sidebar
				'SL Slider Options', //menu page title
				'SL Slider', //menu title
				'manage_options', //type of capability the WP user needs to access this page
				'sl_slider_admin',
				array($this, 'sl_slider_settings_page'), //Method defined below
				'dashicons-images-alt2'
			);
			
			add_submenu_page( //adds the submenu pages to the sidebar inside the menu item.
				'sl_slider_admin',
				'Manage Slides',
				'Manage Slides',
				'manage_options',
				'edit.php?post_type=sl-slider',
				null,
				null
			);
			
			add_submenu_page( //adds the submenu pages to the sidebar inside the menu item.
				'sl_slider_admin',
				'Add New Slide',
				'Add New Slide',
				'manage_options',
				'post-new.php?post_type=sl-slider',
				null,
				null
			);
		} 
		
		//method for add_menu() callback above 
		public function sl_slider_settings_page(){ 
			//create a file inside the views folder for our settings.
			// --- check if the user has capability to access the page 
			if (! current_user_can( 'manage_options' ) ){ // <---- if the user can't manage the options, return.
				return;
			}
			//trigger to show 'settings saved' validation message 
			if ( isset( $_GET['settings-updated'] ) ){
				add_settings_error('sl_slider_options', 'sl_slider_message', 'Settings Saved Successfully', 'success' );
			}
			settings_errors('sl_slider_options');
			require( SL_SLIDER_PATH . 'views/settings-page.php' );	
		}
		
		public function register_scripts(){
			// Flexslider main JQuery Register
			wp_register_script('sl-slider-main-jq', SL_SLIDER_URL . 'vendor/flexslider/jquery.flexslider-min.js', array( 'jquery' ), SL_SLIDER_VERSION, true);
			
			// Register Flexslider JS file
			// -- moved to /functions/functions.php 
			
			// Flexslider css Register
			wp_register_style('sl-slider-main-css', SL_SLIDER_URL . 'vendor/flexslider/flexslider.css', array(), SL_SLIDER_VERSION, 'all');
			
			// custom css register
			wp_register_style('sl-slider-frontend-css', SL_SLIDER_URL . 'assets/css/frontend.css', array(), SL_SLIDER_VERSION, 'all');
			
			// these above scripts are enqueued in class.sl-slider-shortcode.php. Ensures they are only enqueued when we have a post of this type. Reduces unneeded code.
			
			
		}
		
		// scripts/styles for admin settings
		public function register_admin_scripts(){
			global $typenow;
			if ( $typenow == 'sl-slider' ){
				wp_enqueue_style( 'sl-slider-admin', SL_SLIDER_URL . 'assets/css/admin.css' );
			}
		}
		
	}
}

if ( class_exists( 'SL_Slider' ) ) {
	register_activation_hook( __FILE__, array( 'SL_Slider', 'activate') );
	register_deactivation_hook( __FILE__, array( 'SL_Slider', 'deactivate') );
	register_uninstall_hook( __FILE__, array( 'SL_Slider', 'uninstall') );
	
	$sl_slider = new SL_Slider();
}
