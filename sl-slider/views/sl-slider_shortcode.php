<!-- Call the title we have set in sl slider settings page. (Class_name::attribute['']) -->
<!-- If the value of the the shortcode content is not empty, call it first. If it is empty, use the title set in the Plugin Options -->
<h3><?php echo (!empty ( $content ) ) ? esc_html( $content ) : esc_html( SL_Slider_Settings::$options['sl_slider_title'] ); ?></h3>
<div class="sl-slider flexslider ">
	<ul class="slides">
		<?php 
		
		$args = array(
			'post_type' => 'sl-slider',
			'post_status' => 'publish',
			'post__in' => $id,
			'orderby' => $orderby,
		);
		
		$my_query = new WP_Query( $args );
		
		if( $my_query->have_posts() ):
			while($my_query->have_posts() ) : $my_query->the_post();
				
				// Getting data from metaboxes
				$button_text = get_post_meta( get_the_ID(), 'sl_slider_link_text', true );
				$button_url = get_post_meta( get_the_ID(), 'sl_slider_link_url', true );
		?>
		<li>
			<?php the_post_thumbnail( 'full', array( 'class' => 'img-fluid' ) ); ?>
			<div class="sls-container">
				<div class="slider-details-container">
					<div class="wrapper">
						<div class="slider-title">
							<h2><?php the_title();?></h2>	
						</div>
						<div class="slider-description">
							<div class="subtitle"><?php the_content(); ?></div>
							<a class="link" href="<?=esc_attr( $button_url );?>"><?= esc_html( $button_text );?></a>
						</div>
					</div>
				</div>              
			</div>
		</li>
		<?php 
			endwhile;
			wp_reset_postdata();
		endif; 
		?>
	</ul>
</div>
