<?php 
// 10. Get results from metaboxes to show/save in post editor
// ---- Replace all dynamic values with their respective placeholders. (INT Values are %d, Floats are %f, Strings are %s, and Percent Signs are %%). 
global $wpdb;
// $wpdb->prepare() accepts 2 parameters. 1. The SQL Query with the correct placeholder value, and the value that is targeted by said placeholder value.
$query = $wpdb->prepare(
	"SELECT * FROM $wpdb->translationmeta
		WHERE translation_id = %d",
		$post->ID
);
$results = $wpdb->get_results( $query, ARRAY_A );
// var_dump($results);


?>


<table class="form-table sl-translations-metabox"> 
	<!-- Nonce -->
	<input type="hidden" name="sl_translations_nonce" value="<?php echo wp_create_nonce( 'sl_translations_nonce' ); ?>">
	<tr>
		<th>
			<label for="sl_translations_transliteration"><?php esc_html_e( 'Has transliteration?', 'sl-translations' ); ?></label>
		</th>
		<td>
			<select name="sl_translations_transliteration" id="sl_translations_transliteration">
				<option value="Yes" <?php if( isset( $results[0]['meta_value'] ) ) selected( $results[0]['meta_value'], 'Yes' );?> ><?php esc_html_e( 'Yes', 'sl-translations' )?></option>';
				<option value="No" <?php if( isset( $results[0]['meta_value'] ) ) selected( $results[0]['meta_value'], 'No' );?> ><?php esc_html_e( 'No', 'sl-translations' )?></option>';
			</select>            
		</td>
	</tr>
	<tr>
		<th>
			<label for="sl_translations_video_url"><?php esc_html_e( 'Video URL', 'sl-translations' ); ?></label>
		</th>
		<td>
			<!-- 11. Get meta_values and attach to value attribute of my fields. Get values by var_dump()ing way above. -->
			<input 
				type="url" 
				name="sl_translations_video_url" 
				id="sl_translations_video_url" 
				class="regular-text video-url"
				value="<?php echo ( isset ( $results[1]['meta_value'] ) ) ? esc_url( $results[1]['meta_value']) : ""; ?>"
			>
		</td>
	</tr> 
</table>