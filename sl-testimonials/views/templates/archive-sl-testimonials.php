<!-- archive testimonial template -->
<?php get_header(); ?>
<div class="sl-testimonials-archive">
	<header class="page-header">
		<?php the_archive_title( '<h1 class="page-title">', '</h1>' ); ?>
	</header>
		<?php 
		// Create a loop to display the contents of our widget. 
			$testimonials = new WP_Query(
				array(
					'post_type' => 'sl-testimonials',
					'posts_per_page' => -1,
					'post_status' => 'publish',
				)
			);
			
			if ( $testimonials-> have_posts() ) :
				while( $testimonials->have_posts() ) :
					$testimonials->the_post(); 
					
					// Call the fields from our metaboxes inside the loop 
					$url_meta = get_post_meta( get_the_ID(), 'sl_testimonials_user_url', true );
					$occupation_meta = get_post_meta( get_the_ID(), 'sl_testimonials_occupation', true );
					$company_meta = get_post_meta( get_the_ID(), 'sl_testimonials_company', true );
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<div class="testimonial-item">
					<div class="title">
						<!-- Testimonial Title -->
						<h2><a href="<?php the_permalink();?>"><?php the_title(); ?></a></h2>
					</div>
					<div class="content">
						<!-- Image -->
						<div class="thumb">
						<?php if (has_post_thumbnail()){
							the_post_thumbnail( array( 200, 200 ), array( 'class' => 'img-fluid' ) ); 
						} ?>
						</div>
						<!-- Testimonial Content -->
						<?php the_content(); ?>
					</div>
					<div class="meta">
						<!-- Call the fields from our metaboxes inside the loop  -->
						<span class="occupation"><?php echo esc_html( $occupation_meta );?></span>
						<span class="company"><a href="<?php echo esc_attr( $url_meta ) ?>"><?php echo esc_html( $company_meta );?></a></span>
						
					</div>
				</div>
			</article>
			<?php 
				endwhile;
				wp_reset_postdata();
			endif;
		?>
</div>
<?php get_footer(); ?>