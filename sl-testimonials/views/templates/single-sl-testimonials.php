<!-- single testimonial template -->
<?php get_header(); ?>
<div class="sl-testimonials-single">
	<header class="entry-header">
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
	</header>
		<?php 
		// Create a loop to display a single testimonial
			
				while( have_posts() ) :
					the_post(); 
					
					// Call the fields from our metaboxes inside the loop 
					$url_meta = get_post_meta( get_the_ID(), 'sl_testimonials_user_url', true );
					$occupation_meta = get_post_meta( get_the_ID(), 'sl_testimonials_occupation', true );
					$company_meta = get_post_meta( get_the_ID(), 'sl_testimonials_company', true ); 
					
				?>
					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<div class="testimonial-item">
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
				<?php endwhile; ?>
</div>
<?php get_footer(); ?>