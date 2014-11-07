<?php

global $post;

wp_nonce_field( 'orbis_save_hosting_group_keychains', 'orbis_hosting_group_keychains_meta_box_nonce' );

$keychain_id = get_post_meta( $post->ID, '_orbis_hosting_group_control_panel_keychain_id', true );

?>
<table class="form-table">
	<tr valign="top">
		<th scope="row">
			<label for="orbis_hosting_group_control_panel_keychain_id"><?php _e( 'Control Panel', 'orbis_keychains' ); ?></label>
		</th>
		<td>
			<input id="orbis_hosting_group_control_panel_keychain_id" name="_orbis_hosting_group_control_panel_keychain_id" value="<?php echo esc_attr( $keychain_id ); ?>" type="text" class="regular-text" />
		</td>
	</tr>
</table>