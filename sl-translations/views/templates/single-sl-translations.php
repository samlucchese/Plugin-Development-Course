<?php get_header(); ?>

<div class="wrap">
	<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
	
			<?php
			
			global $current_user;
			global $wpdb;
			
			//only leaving meta_value because we can get the other values without the query using built-in functions 
			$q = $wpdb->prepare(
				"SELECT meta_value
				FROM $wpdb->posts AS p
				INNER JOIN $wpdb->translationmeta AS tm
				ON p.ID = tm.translation_id
				WHERE p.ID = %d",
				get_the_ID()
			);
			// Adding another parameter to the get_results() function, ARRAY_A. We want the output of get_results() to be an array.
			$results = $wpdb->get_results( $q, ARRAY_A );
			// This adds the css class to the <article> tag based on what is set in the metabox field (Yes or No)
			$has_transliteration = $results[0]['meta_value'] == "Yes" ? "has-transliteration" : "";
			// var_dump( $results );

			
			
			while( have_posts() ): 
				the_post();
				?>
					<article id="post-<?php the_ID(); ?>" <?php post_class( $has_transliteration ); ?>>
						<div class="translation-item">                   
							<div class="content">
								<?php do_action( 'slt_content' ); ?>
								
							</div>
						</div>
					</article>
				<?php endwhile; ?>
			</main>
		</div>
	</div>
<?php
get_footer();