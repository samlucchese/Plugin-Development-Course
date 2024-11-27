<?php 

class SL_Testimonials_Widget extends WP_Widget{
	// 12. Create the __construct(), form(), widget() and update() methods
	// All widgets must have all of these methods
	public function __construct(){
		$widget_options = array(
			'description' => __( 'Your most beloved testimonials', 'sl-testimonials' )
		);
		
		parent::__construct(
			'sl-testimonials',
			'SL Testimonials',
			$widget_options,
		);
		
		// add action to register and use the widget
		add_action(
			'widgets_init', function(){
				register_widget(
					'SL_Testimonials_Widget',
				);
			}
		);
		// 19b. Enqueue styles ONLY IF widget is active 
		if(is_active_widget( false, false, $this->id_base )){
			add_action( 'wp_enqueue_scripts', array($this, 'enqueue' ) );
		}
	}
	// 19a. enqueue styles method 
	public function enqueue(){
		wp_enqueue_style(
			'sl-testimonials-style-css',
			SL_TESTIMONIALS_URL . 'assets/css/frontend.css',
			array(),
			SL_TESTIMONIALS_VERSION,
			'all',
		);
	}
	
	
	// All widgets must have all of these methods
	public function form( $instance ){
		// Decide what fields we need for our form [Title, # of testimonials, Show User Image?, Show user Occupation?, Show User Company?]
		// 14a. Re-set the defaults for our fields
		$title = isset( $instance['title'] ) ? $instance['title'] : '';
		$number = isset( $instance['number'] ) ? (int) $instance['number'] : 5; 
		$image = isset( $instance['image'] ) ? (bool) $instance['image'] : false; 
		$occupation = isset( $instance['occupation'] ) ? (bool) $instance['occupation'] : false; ;
		$company = isset( $instance['company'] ) ? (bool) $instance['company'] : false; ;
		?> 
		<!-- // now add HTML for form fields -->
		<!-- Title -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title', 'sl-testimonials'); ?>:</label>
			<input 
				type="text" 
				class="widefat" 
				id="<?php echo $this->get_field_id( 'title' ); ?>"
				name="<?php echo $this->get_field_name( 'title' ); ?>" 
				value="<?php echo esc_attr( $title ); ?>"
			>
		</p>
		<!-- Number -->
		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php esc_html_e( 'Number of Testimonials to Show', 'sl-testimonials'); ?>:</label>
			<input 
				type="number" 
				class="tiny-text" 
				id="<?php echo $this->get_field_id( 'number' ); ?>"
				name="<?php echo $this->get_field_name( 'number' ); ?>"
				step="1"
				min="1"
				size="3" 
				value="<?php echo esc_attr( $number ); ?>"
			>
		</p>
		<!-- Image -->
		<p>
			<input 
				type="checkbox" 
				class="checkbox" 
				id="<?php echo $this->get_field_id( 'image' ); ?>"
				name="<?php echo $this->get_field_name( 'image' ); ?>"
				<?php checked( $image ); ?>
			>
			<label for="<?php echo $this->get_field_id( 'image' ); ?>"><?php esc_html_e( 'Display User Image?', 'sl-testimonials'); ?>:</label>
		</p>
		<!-- Occupation -->
		<p>
			<input 
				type="checkbox" 
				class="checkbox" 
				id="<?php echo $this->get_field_id( 'occupation' ); ?>"
				name="<?php echo $this->get_field_name( 'occupation' ); ?>"
				<?php checked( $occupation ); ?>
			>
			<label for="<?php echo $this->get_field_id( 'occupation' ); ?>"><?php esc_html_e( 'Display User Occupation?', 'sl-testimonials'); ?>:</label>
		</p>
		<!-- Company -->
		<p>
			<input 
				type="checkbox" 
				class="checkbox" 
				id="<?php echo $this->get_field_id( 'company' ); ?>"
				name="<?php echo $this->get_field_name( 'company' ); ?>"
				<?php checked( $company ); ?>
			>
			<label for="<?php echo $this->get_field_id( 'company' ); ?>"><?php esc_html_e( 'Display User Company?', 'sl-testimonials'); ?>:</label>
		</p>
		<?php
	}
	
	// All widgets must have all of these methods
	// F. The method to display the widget on the front-end
	public function widget( $args, $instance ){
		// 15. Set up default values so that info will display on front end if we drag the widget without adding content
		$default_title = 'SL Testimonials';
		$title = ! empty( $instance['title'] ) ? $instance['title'] : $default_title;
		$number = ! empty( $instance['number'] ) ? $instance['number'] : 5;
		$image = isset( $instance['image'] ) ? $instance['image'] : false;
		$occupation = isset( $instance['occupation'] ) ? $instance['occupation'] : false;
		$company = isset( $instance['company'] ) ? $instance['company'] : false;
		
		// Start assembling the widget with our pieces
		echo $args['before_widget']; 
		echo $args['before_title'] . $title . $args['after_title'];
		// 16. Create a file in the /views/ directory for our widget display set-up and create a loop to display the contents of our widget. 
		require( SL_TESTIMONIALS_PATH . 'views/sl-testimonials_widget.php' );
		echo $args['after_widget'];
		
		
	}
	
	// All widgets must have all of these methods
	// 14. Save Widget configuration data
	public function update( $new_instance, $old_instance ){
		$instance = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['number'] = (int) $new_instance['number'];
		$instance['image'] = ! empty ( $new_instance['image'] ) ? 1 : 0; // if the value of the new instance is not empty, the updated value will be true(1). otherwise, it will get false(0).
		$instance['occupation'] = ! empty ( $new_instance['occupation'] ) ? 1 : 0; // if the value of the new instance is not empty, the updated value will be true(1). otherwise, it will get false(0).
		$instance['company'] = ! empty ( $new_instance['company'] ) ? 1 : 0; // if the value of the new instance is not empty, the updated value will be true(1). otherwise, it will get false(0).
		return $instance;
	}
}