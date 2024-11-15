 <!-- All admin pages are wrapped in a <div class="wrap"></div> -->
<div class="wrap">
	
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1> <!-- Gets the menu_title value from what is set in add_menu()-> add_menu_page() in sl-slider.php-->
	
	<form action="options.php" method="post"> <!-- Create the form we see when we access the options page  -->
		<!-- Call the sections, form fields, and a button to submit the settings form data. -->
		<?php
			settings_fields( 'sl_slider_group');
			do_settings_sections( 'sl_slider_page1' );
			do_settings_sections( 'sl_slider_page2' );
			submit_button( 'Save Settings' );
		?>		
	</form>
</div>