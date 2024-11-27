<?php 
// 17. Create a loop to display the contents of our widget. 
	$testimonials = new WP_Query(
		array(
			'post_type' => 'sl-testimonials',
			'posts_per_page' => $number,
			'post_status' => 'publish',
		)
	);
	
	if ( $testimonials-> have_posts() ) :
		while( $testimonials->have_posts() ) :
			$testimonials->the_post(); 
			
			// 18. Call the fields from our metaboxes inside the loop 
			$url_meta = get_post_meta( get_the_ID(), 'sl_testimonials_user_url', true );
			$occupation_meta = get_post_meta( get_the_ID(), 'sl_testimonials_occupation', true );
			$company_meta = get_post_meta( get_the_ID(), 'sl_testimonials_company', true );
	?>
		<div class="testimonial-item">
			<div class="title">
				<!-- Testimonial Title -->
				<h3><?php the_title(); ?></h3>
			</div>
			<div class="content">
				<!-- Image -->
				<?php if ( $image ) : ?>
					<div class="thumb">
					<?php if (has_post_thumbnail()){
						the_post_thumbnail( array( 70, 70 ) ); 
					} ?>
				</div>
					<?php endif; ?>
				<!-- Testimonial Content -->
				<?php the_content(); ?>
			</div>
			<div class="meta">
				<!-- 18a. Call the fields from our metaboxes inside the loop  -->
				<?php if ($occupation): ?>
					<span class="occupation"><?php echo esc_html( $occupation_meta );?></span>
				<?php endif; ?>
				<?php if ($company): ?>
					<span class="company"><a href="<?php echo esc_attr( $url_meta ) ?>"><?php echo esc_html( $company_meta );?></a></span>
				<?php endif; ?>
				
			</div>
		</div>
	<?php 
		endwhile;
		wp_reset_postdata();
	endif;
?>
<!-- 20. Set up archive page/link for all testimonials -->
<a href="<?php echo get_post_type_archive_link( 'sl-testimonials' ); ?>"><?php echo esc_html( 'See More Testimonials', 'sl-testimonials' ); ?></a>