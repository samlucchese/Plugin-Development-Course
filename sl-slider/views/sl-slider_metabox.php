<!-- Metabox will display whatever is set in here -->
<!-- In this case, two fields: Link Text and Link URL 
 -->
 
 
 <?php 
 $meta = get_post_meta($post->ID);
 $link_text = get_post_meta( $post->ID, 'sl_slider_link_text', true);
 $link_url = get_post_meta( $post->ID, 'sl_slider_link_url', true);
 
 
// var_dump($link_text, $link_url); //How we get the value  that we echo below. Inspect and look at the array.
 
 ?>
<table class="form-table sl-slider-metabox"> 
<input type="hidden" name="sl_slider_nonce" value="<?php echo wp_create_nonce( "sl_slider_nonce" );?>"
	<tr>
		<th>
			<label for="sl_slider_link_text">Link Text: </label>
		</th>
		<td>
			<input 
				type="text" 
				name="sl_slider_link_text" 
				id="sl_slider_link_text" 
				class="regular-text link-text"
				value="<?php echo ( isset ($link_text) ) ? esc_html( $link_text ) : ''; ?>"
				required
			>
		</td>
	</tr>
	<tr>
		<th>
			<label for="sl_slider_link_url">Link URL: </label>
		</th>
		<td>
			<input 
				type="url" 
				name="sl_slider_link_url" 
				id="sl_slider_link_url" 
				class="regular-text link-url"
				value="<?php echo ( isset ( $link_url) ) ? esc_url( $link_url ) : '' ; ?>"
				required
			>
		</td>
	</tr>               
</table>