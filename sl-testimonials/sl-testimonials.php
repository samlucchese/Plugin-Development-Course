<?php

/**
* Plugin Name: SL Testimonials
* Plugin URI: https://www.wordpress.org/sl-testimonials
* Description: My plugin's description
* Version: 1.0
* Requires at least: 5.6
* Requires PHP: 7.0
* Author: Sam Lucchese
* Author URI: https://www.codigowp.net
* License: GPL v2 or later
* License URI: https://www.gnu.org/licenses/gpl-2.0.html
* Text Domain: sl-testimonials
* Domain Path: /languages
*/
/*
SL Testimonials is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
SL Testimonials is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with SL Testimonials. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( !class_exists( 'SL_Testimonials' ) ){

    class SL_Testimonials{

        public function __construct() {

            // Define constants used througout the plugin
            $this->define_constants();           
            
            // 1b. Instantiate new class
            require_once( SL_TESTIMONIALS_PATH . 'post-types/class.sl-testimonials-cpt.php' );
            $SLTestimonialsPostType = new SL_Testimonials_Post_Type();
            
            // 13. Instantiate new Widgets class
            require_once( SL_TESTIMONIALS_PATH . 'widgets/class.sl-testimonials-widget.php' );
            $SLTestimonialsWidget = new SL_Testimonials_Widget();

        }

         /**
         * Define Constants
         */
        public function define_constants(){
            // Path/URL to root of this plugin, with trailing slash.
            define ( 'SL_TESTIMONIALS_PATH', plugin_dir_path( __FILE__ ) );
            define ( 'SL_TESTIMONIALS_URL', plugin_dir_url( __FILE__ ) );
            define ( 'SL_TESTIMONIALS_VERSION', '1.0.0' );     
        }

        /**
         * Activate the plugin
         */
        public static function activate(){
            update_option('rewrite_rules', '' );
        }

        /**
         * Deactivate the plugin
         */
        public static function deactivate(){
            // 4. unregister the cpt when the plugin is deactivated
            unregister_post_type( 'sl-testimonials' );
            flush_rewrite_rules();
        }

        /**
         * Uninstall the plugin
         */
        public static function uninstall(){

        }

    }
}

if( class_exists( 'SL_Testimonials' ) ){
    // Installation and uninstallation hooks
    register_activation_hook( __FILE__, array( 'SL_Testimonials', 'activate'));
    register_deactivation_hook( __FILE__, array( 'SL_Testimonials', 'deactivate'));
    register_uninstall_hook( __FILE__, array( 'SL_Testimonials', 'uninstall' ) );

    $sl_testimonials = new SL_Testimonials();
}