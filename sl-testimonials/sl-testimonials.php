<?php

/**
* Plugin Name: SL Testimonials
* Plugin URI: https://www.wordpress.org/sl-testimonials
* Description: Creates a new Custom Post Type called 'Testimonials'. It also creates a custom template for the Testimonials Archive page and Testimonials Single Post. This plugin is a widget. 
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
            
            // 28. Translation support, function defined below.
            $this->load_textdomain();
            
            // Define constants used througout the plugin
            $this->define_constants();           
            
            // 1b. Instantiate new class
            require_once( SL_TESTIMONIALS_PATH . 'post-types/class.sl-testimonials-cpt.php' );
            $SLTestimonialsPostType = new SL_Testimonials_Post_Type();
            
            // 13. Instantiate new Widgets class
            require_once( SL_TESTIMONIALS_PATH . 'widgets/class.sl-testimonials-widget.php' );
            $SLTestimonialsWidget = new SL_Testimonials_Widget();
            
            // 21. filter for archive template, method down below
            add_filter( 'archive_template', array( $this, 'load_custom_archive_template') );
            // 23. Repeat 21 for single template, method down below
            add_filter( 'single_template', array( $this, 'load_custom_single_template') );
        }

         /**
         * Define Constants
         */
        public function define_constants(){
            // Path/URL to root of this plugin, with trailing slash.
            define ( 'SL_TESTIMONIALS_PATH', plugin_dir_path( __FILE__ ) );
            define ( 'SL_TESTIMONIALS_URL', plugin_dir_url( __FILE__ ) );
            define ( 'SL_TESTIMONIALS_VERSION', '1.0.0' );  
            
            // 25. Define the override directory the plugin will search for in the theme regardless of which theme is active 
            define( 'SL_TESTIMONIALS_OVERRIDE_PATH_DIR', get_stylesheet_directory() . '/sl-testimonials/' );   
        }
        
        // 22. Method for archive template files 
        public function load_custom_archive_template( $tpl ) { //tpl is template
            if( current_theme_supports('sl-testimonials') ){
                if ( is_post_type_archive( 'sl-testimonials' ) ) {
                    $tpl = $this->get_template_part_location( 'archive-sl-testimonials.php' );
                }
            }
            return $tpl;
        }
        // 24. Repeat 22 for single template, Method for single template files 
        public function load_custom_single_template( $tpl ) { //tpl is template
            if( current_theme_supports('sl-testimonials') ){
                if ( is_singular( 'sl-testimonials' ) ) {
                    $tpl = $this->get_template_part_location( 'single-sl-testimonials.php' );
                }
            }
            return $tpl;
        }
        
        // 26. Method for override function 
        public function get_template_part_location( $file ){
            if( file_exists( SL_TESTIMONIALS_OVERRIDE_PATH_DIR . $file ) ) {
                $file = SL_TESTIMONIALS_OVERRIDE_PATH_DIR . $file;
            }else{
                $file = SL_TESTIMONIALS_PATH . 'views/templates/' . $file;
            }
            return $file;
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
            // Remove options from the database wp_options table named widget_sl-testimonials 
            delete_option( 'widget_sl-testimonials' );
            // Fetch and delete all posts of type 'sl-slider', including auto-drafts
            $posts = get_posts(
                array(
                    'post_type'   => 'sl-testimonials',
                    'numberposts' => -1,
                    'post_status' => array( 'any', 'auto-draft' )
                )
            );
            foreach ( $posts as $post ) {
                wp_delete_post( $post->ID, true );
            }
        }
        
        // 29. Function to add Translation support to plugin. Call it above in __construct(){
        public function load_textdomain(){
            load_plugin_textdomain(
                'sl-testimonials', //text domain, found at top of file commented out
                false,
                dirname( plugin_basename(__FILE__) ) . '/languages/'
            );
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