 <!-- All admin pages are wrapped in a <div class="wrap"></div> -->
<div class="wrap">
	
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1> <!-- Gets the menu_title value from what is set in add_menu()-> add_menu_page() in sl-slider.php-->
	
	<!-- Adds active tab -->
	<!-- if the tab parameter is set, its value will be whatever is found in the URL. Otherwise, it will be set to main_options. -->
	<?php 
		$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET['tab'] : 'main_options';
	?>
	
	
	<!-- //additional tab -->
	<h2 class="nav-tab-wrapper">
		<!-- Adds class to tab that gives it active state -->
		<a href="?page=sl_slider_admin&tab=main_options" class="nav-tab <?=$active_tab == 'main_options' ? 'nav-tab-active' : ''; ?>">Main Options</a>
		<a href="?page=sl_slider_admin&tab=additional_options" class="nav-tab <?=$active_tab == 'additional_options' ? 'nav-tab-active' : ''; ?>">Additional Options</a>
	</h2>
	
	<form action="options.php" method="post"> <!-- Create the form we see when we access the options page  -->
		<!-- Call the sections, form fields, and a button to submit the settings form data. -->
		<?php
			// Separates the settings onto different tabs
			if ( $active_tab == 'main_options' ) {
				settings_fields( 'sl_slider_group');
				do_settings_sections( 'sl_slider_page1' );
			} else {
				settings_fields( 'sl_slider_group');
				do_settings_sections( 'sl_slider_page2' );
				
			}
			submit_button( 'Save Settings' );
		?>		
	</form>
</div>