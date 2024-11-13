<?php 

if ( ! class_exists( 'SL_Slider_Settings' ) ) {
	class SL_Slider_Settings{
		
		// create attributes. 
		// ---> (public, static added because i want to access this attribute outside this class.)
		public static $options;
		
		public function __construct() {
			
			// The below line is looking for a value called 'sl_slider_options' in the DB table (adminerEvo, phpmyadmin). Creates a variable ($options) that contains an array with all the values submitted by our form. (sl_slider_options)
			self::$options = get_option( 'sl_slider_options' );
			
			add_action( 'admin_init', array( $this, 'admin_init') ); // method below 
		}
		
		public function admin_init(){
			register_setting( 'sl_slider_group', 'sl_slider_options' );
			
			// create 2 sections and 4 fields split between these 2 sections 
			// Create page1 section below 
			add_settings_section(
				'sl_slider_main_section',
				'How does it work?',
				null,
				'sl_slider_page1' //Creates a new slider settings page called sl_slider_page1
			);
			// create page2 section 
			add_settings_section(
				'sl_slider_second_section',
				'Other Plugin Options',
				null,
				'sl_slider_page2' //Creates a new slider settings page called sl_slider_page2
			);
			// Create page1 fields below
			add_settings_field(
				'sl_slider_shortcode',
				'Shortcode',
				array($this, 'sl_slider_shortcode_callback'),
				'sl_slider_page1',
				'sl_slider_main_section',
			);
			// Create page2 fields below
			add_settings_field(
				'sl_slider_title',
				'Slider Title',
				array($this, 'sl_slider_title_callback'),
				'sl_slider_page2',
				'sl_slider_second_section',
			);
			
			// Create Checkbox field for bullets below 
			add_settings_field(
				'sl_slider_bullets',
				'Display Bullets?',
				array($this, 'sl_slider_bullets_callback'),
				'sl_slider_page2',
				'sl_slider_second_section',
			);
			
			// Create field for slider style below 
			add_settings_field(
				'sl_slider_style',
				'Slider Style',
				array($this, 'sl_slider_style_callback'),
				'sl_slider_page2',
				'sl_slider_second_section',
			);
		}
		
		// Decides WHAT will be inside of add_settings_field(). Called in callback in add_settings_field().
		public function sl_slider_shortcode_callback(){
			?>
			<span>Use the shortcode [sl_slider] to display the slider in any page/post/widget.</span>
			<?php
		}
		
		// callback to display title field
		public function sl_slider_title_callback(){
			?>
				<input 
				type="text" 
				name="sl_slider_options[sl_slider_title]"
				id="sl_slider_title" 
				value="<?php echo isset( self::$options['sl_slider_title'] ) ? esc_attr( self::$options['sl_slider_title'] ) : ''; ?>"
				>
			<?php 
		}
		
		// callback to display slider bullets field
		public function sl_slider_bullets_callback(){
			?>
				<input 
				type="checkbox" 
				name="sl_slider_options[sl_slider_bullets]"
				id="sl_slider_bullets" 
				value="1"
				<?php 
					if( isset( self::$options['sl_slider_bullets'] ) ){
						checked( "1", self::$options['sl_slider_bullets'], true );
					} 
				?>
				/>
				<label for="sl_slider_bullets">Whether to display bullets or not.</label>
			<?php 
		}
		
		// callback to display slider style fields 
		public function sl_slider_style_callback(){
			?>
				<select
					id="sl_slider_style"
					name="sl_slider_options[sl_slider_style]" >
					
					<!-- isset function says: If the style-1/2 exists in the database, we will call the selected function. Otherwise this field is left with no value. -->
					<option value="style-1" <?php isset( self::$options['sl_slider_style'] ) ? selected( 'style-1', self::$options['sl_slider_style'], true) : ''; ?>>
						Style-1
					</option>
					<option value="style-2" <?php isset( self::$options['sl_slider_style'] ) ? selected( 'style-2', self::$options['sl_slider_style'], true) : ''; ?>>
						Style-2
					</option>
				</select>
			<?php 
		}
		
	}
}
