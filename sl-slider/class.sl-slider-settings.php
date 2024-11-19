<?php 
// 6. Create class for Slider Settings 
if ( ! class_exists( 'SL_Slider_Settings' ) ) {
	class SL_Slider_Settings{
		
		// create attributes. 
		// ---> (public, static added because i want to access this attribute outside this class.)
		public static $options;
		
		public function __construct() {
			
			// The below line is looking for a value called 'sl_slider_options' in the DB table (adminerEvo, phpmyadmin). Creates a variable ($options) that contains an array with all the values submitted by our form. (sl_slider_options)
			// --- See this below line in action to call the SLIDER TITLE in sl-slider_shortcode.php
			self::$options = get_option( 'sl_slider_options' );
			
			add_action( 'admin_init', array( $this, 'admin_init') ); // method below 
		}
		
		public function admin_init(){
			register_setting( 'sl_slider_group', 'sl_slider_options', array( $this, 'sl_slider_validate' ) );
			
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
				array(
					'label_for' => 'sl_slider_title',
				),
			);
			
			// Create Checkbox field for bullets below 
			add_settings_field(
				'sl_slider_bullets',
				esc_html__( 'Display Bullets?', 'sl-slider' ),
				array($this, 'sl_slider_bullets_callback'),
				'sl_slider_page2',
				'sl_slider_second_section',
				array(
					'label_for' => 'sl_slider_bullets',
				),
			);
			
			// Create field for slider style below 
			add_settings_field(
				'sl_slider_style',
				'Slider Style',
				array($this, 'sl_slider_style_callback'),
				'sl_slider_page2',
				'sl_slider_second_section',
				array(
					'items' => array(
						'style-1',
						'style-2'
					),
					'label_for' => 'sl_slider_style'
				)
			);
		}
		
		// Decides WHAT will be inside of add_settings_field(). Called in callback in add_settings_field().
		public function sl_slider_shortcode_callback(){
			?>
			<span>Use the shortcode [sl_slider] to display the slider in any page/post/widget.</span>
			<?php
		}
		
		// callback to display title field
		public function sl_slider_title_callback( $args ){
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
		public function sl_slider_bullets_callback( $args ){
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
		public function sl_slider_style_callback( $args ){
			?>
				<select
					id="sl_slider_style"
					name="sl_slider_options[sl_slider_style]" >
					
					<!-- isset function says: If the style-1/2 exists in the database, we will call the selected function. Otherwise this field is left with no value. -->
					<?php foreach( $args['items'] as $item ) : ?>
						<option value="<?php echo esc_attr($item); ?>"
							<?php 
							isset( self::$options['sl_slider_style'] ) ? selected ( $item, self::$options['sl_slider_style'], true ) : ''; 
							?>
						>
							<?php echo esc_html( ucfirst( $item ) ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			<?php 
		}
		
		public function sl_slider_validate( $input ){
			$new_input = get_option('sl_slider_options');
			foreach( $input as $key => $value ) {
				switch($key){
					case 'sl_slider_title':
						if( empty( $value)){
							add_settings_error('sl_slider_options', 'sl_slider_message', 'The title field cannot be left empty', 'error');
							$value = 'Please type some text';
						}
						$new_input[$key] = sanitize_text_field( $value );
					break;
					default:
						$new_input[$key] = sanitize_text_field( $value );
					break;
				}
			}
			// Ensure unset checkbox keys are saved as 0
			if ( ! isset( $input['sl_slider_bullets'] ) ) {
				$new_input['sl_slider_bullets'] = 0;
			}
			return $new_input;
		}
		
	}
}
