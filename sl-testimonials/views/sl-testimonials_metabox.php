<?php 
	// 11. get_post_meta for nonces. Attach to 'value' attributes below.
	$occupation = get_post_meta( $post->ID, 'sl_testimonials_occupation', true);
	$company = get_post_meta( $post->ID, 'sl_testimonials_company', true);
	$user_url = get_post_meta( $post->ID, 'sl_testimonials_user_url', true);
?>


<table class="form-table sl-testimonials-metabox"> 
	<!-- 10. Add input field that generates security nonce to /views/sl-testimonials_metabox.php -->
	<input type="hidden" name="sl_testimonials_nonce" value="<?php echo wp_create_nonce( "sl_testimonials_nonce" );?>"
	<tr>
		<th>
			<label for="sl_testimonials_occupation"><?php esc_html_e( 'User occupation', 'sl-testimonials' ); ?></label>
		</th>
		<td>
			<input 
				type="text" 
				name="sl_testimonials_occupation" 
				id="sl_testimonials_occupation" 
				class="regular-text occupation"
				value="<?php echo ( isset( $occupation ) ) ? esc_html( $occupation ) : ''; ?>"
			>
		</td>
	</tr>
	<tr>
		<th>
			<label for="sl_testimonials_company"><?php esc_html_e( 'User company', 'sl-testimonials' ); ?></label>
		</th>
		<td>
			<input 
				type="text" 
				name="sl_testimonials_company" 
				id="sl_testimonials_company" 
				class="regular-text company"
				value="<?php echo ( isset( $company ) ) ? esc_html( $company ) : ''; ?>"
			>
		</td>
	</tr>
	<tr>
		<th>
			<label for="sl_testimonials_user_url"><?php esc_html_e( 'User URL', 'sl-testimonials' ); ?></label>
		</th>
		<td>
			<input 
				type="url" 
				name="sl_testimonials_user_url" 
				id="sl_testimonials_user_url" 
				class="regular-text user-url"
				value="<?php echo ( isset( $user_url ) ) ? esc_url( $user_url ) : ''; ?>"
			>
		</td>
	</tr> 
</table>