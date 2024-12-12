<?php

/**
* Plugin Name: SL Translations
* Plugin URI: https://www.wordpress.org/sl-translations
* Description: This plugin will create a translation for song lyrics. <em><strong>Translations</strong></em> will be the Custom Post Type, <em><strong>Singer</strong></em> will be the taxonomy. Adds this data to a new table called <em>wp_translationmeta</em>.  
* Version: 1.0
* Requires at least: 5.6
* Requires PHP: 7.0
* Author: Sam Lucchese
* Author URI: https://www.codigowp.net
* License: GPL v2 or later
* License URI: https://www.gnu.org/licenses/gpl-2.0.html
* Text Domain: sl-translations
* Domain Path: /languages
*/
/*
SL Translations is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
SL Translations is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with SL Translations. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( !class_exists( 'SL_Translations' )){
    
	class SL_Translations{
        
		public function __construct(){
			$this->define_constants(); 
            
            //  #34. Create /functions/functions.php and require this file 
            require_once( SL_TRANSLATIONS_PATH . 'functions/functions.php' );
            
            // #4. Require the cpt file + instantiate the new class in our main file
            require_once( SL_TRANSLATIONS_PATH . 'post-types/class.sl-translations-cpt.php' );
            $SLTranslationsPostType = new SL_Translations_Post_Type();
            
            // #20. Require the /shortcodes/class.sl-translations-shortcode.php file + instantiate new class from /views/sl-translations-shortcode.php 
            require_once( SL_TRANSLATIONS_PATH . 'shortcodes/class.sl-translations-shortcode.php' );
            $SLTranslationsShortcode = new SL_Translations_Shortcode();
            
            // #45. EDIT SHORTCODE – Require the /shortcodes/class.sl-translations-edit-shortcode.php file + instantiate new class from /views/sl-translations-edit-shortcode.php 
            require_once( SL_TRANSLATIONS_PATH . 'shortcodes/class.sl-translations-edit-shortcode.php' );
            $SLTranslationsEditShortcode = new SL_Translations_Edit_Shortcode();
            
            // #30. Call the hook to enqueue_scripts for our register_scripts method 
            add_action( 'wp_enqueue_scripts', array($this, 'register_scripts'), 999 );
		}
        
		public function define_constants(){
            // Path/URL to root of this plugin, with trailing slash.
			define ( 'SL_TRANSLATIONS_PATH', plugin_dir_path( __FILE__ ) );
            define ( 'SL_TRANSLATIONS_URL', plugin_dir_url( __FILE__ ) );
            define ( 'SL_TRANSLATIONS_VERSION', '1.0.0' );
		}
        
        /**
         * Activate the plugin
         */
        public static function activate(){
            update_option('rewrite_rules', '' );
            
            // #1. Perform a check during plugin activation, if the plugin version info exists, then the plugin is already installed and the table already exists. Otherwise run a query that will create the table in the database and then add a record to the wp_options table with the plugin information. 
            global $wpdb;
            // $table_name = "wp_"; // default prefix, although we might want to change it. 
            $table_name = $wpdb->prefix . "translationmeta"; // in quotes is the new table name. Table will be ' wp_translationmeta '
            
            $slt_db_version = get_option( 'sl_translation_db_version' );
            
            // Get plugin version info if the table exists. If empty, the plugin is not installed. Use SQL To write the query. 
            // ---- PRIMARY KEY and (meta_id) has TWO spaces in between it
            if ( empty( $slt_db_version ) ){
                $query = "
                    CREATE TABLE $table_name (
                        meta_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                        translation_id bigint(20) NOT NULL DEFAULT '0',
                        meta_key varchar(255) DEFAULT NULL,
                        meta_value longtext,
                        PRIMARY KEY  (meta_id), 
                        KEY translation_id (translation_id),
                        KEY meta_key (meta_key)
                    )
                    ENGINE=InnoDB DEFAULT CHARSET=utf8;
                ";
                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' ); // REQUIRED FOR dbDelta function 
                dbDelta( $query ); // this function allows the table to be created if it does not exist. It also allows us to alter an existing table using the correct schema. 
                
                $slt_db_version = '1.0'; // define the db version of the plugin 
                add_option( 'sl_translation_db_version', $slt_db_version ); // send the version string to the wp_options table 
                
            }
            
            // #2. Use $wpdb to see if the 2 pages(submit-translation + edit-translation) exist. If not, create them. 
            if ( $wpdb->get_row( "SELECT post_name FROM {$wpdb->prefix}posts WHERE post_name = 'submit-translation'" ) === null ){
                // If empty, insert a post
                
                $current_user = wp_get_current_user();  // variable for post_author
                
                // post_content ----->  it is the shortcode that we want to make available in this page. Allows us to add this shortcode directly into a shortcode block in the block editor.
                $page = array(
                    'post_title' => __('Submit Translation', 'sl-translations'),
                    'post_name' => 'submit-translation',
                    'post_status' => 'publish', 
                    'post_author' => $current_user->ID,
                    'post_type' => 'page',
                    'post_content' => '<!-- wp:shortcode -->[sl_translations]<!-- /wp:shortcode -->' 
                );
                wp_insert_post( $page ); // Inserts the new page with the parameters defined above in $page 
            }
            if ( $wpdb->get_row( "SELECT post_name FROM {$wpdb->prefix}posts WHERE post_name = 'edit-translation'" ) === null ){
                // If empty, insert a post
                
                $current_user = wp_get_current_user();  // variable for post_author
                
                // post_content ----->  it is the shortcode that we want to make available in this page. Allows us to add this shortcode directly into a shortcode block in the block editor.
                $page = array(
                    'post_title' => __('Edit Translation', 'sl-translations'),
                    'post_name' => 'edit-translation',
                    'post_status' => 'publish', 
                    'post_author' => $current_user->ID,
                    'post_type' => 'page',
                    'post_content' => '<!-- wp:shortcode -->[sl_translations_edit]<!-- /wp:shortcode -->' 
                );
                wp_insert_post( $page ); // Inserts the new page with the parameters defined above in $page 
            }
            
            
        }
        
        /**
         * Deactivate the plugin
         */
        public static function deactivate(){
            flush_rewrite_rules();
            // #6. Unregister post type in deactivate() function
            unregister_post_type( 'sl-translations' );
        }        
        
        /**
         * Uninstall the plugin
         */
        public static function uninstall(){
            
        }       
        
        // #28. and #29. 
        // #28. Create register_scripts() method and the /assets/jquery.custom.js file, register custom script.
        // #29. Download jquery-validation.zip file from https://github.com/jquery-validation/jquery-validation/releases/tag/1.19.5, unzip, copy jquery.validate.min.js file and paste to /assets/. Register validation script 
        public function register_scripts(){
            // register custom script
            wp_register_script( 'custom_js', SL_TRANSLATIONS_URL . 'assets/jquery.custom.js', array( 'jquery' ), SL_TRANSLATIONS_VERSION, true);
            // register validation script
            wp_register_script( 'validate_js', SL_TRANSLATIONS_URL . 'assets/jquery.validate.min.js', array( 'jquery' ), SL_TRANSLATIONS_VERSION, true);
        }
        
    }
}

// Plugin Instantiation
if (class_exists( 'SL_Translations' )){

    // Installation and uninstallation hooks
    register_activation_hook( __FILE__, array( 'SL_Translations', 'activate'));
    register_deactivation_hook( __FILE__, array( 'SL_Translations', 'deactivate'));
    register_uninstall_hook( __FILE__, array( 'SL_Translations', 'uninstall' ) );

    // Instatiate the plugin class
    $sl_translations = new SL_Translations(); 
}