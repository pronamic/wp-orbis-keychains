<?php

global $post;

wp_nonce_field( 'orbis_save_domain_name_keychains', 'orbis_domain_name_keychains_meta_box_nonce' );

$ftp_keychain_id         = get_post_meta( $post->ID, '_orbis_domain_name_ftp_keychain_id', true );
$google_apps_keychain_id = get_post_meta( $post->ID, '_orbis_domain_name_google_apps_keychain_id', true );
$wordpress_keychain_id   = get_post_meta( $post->ID, '_orbis_domain_name_wordpress_keychain_id', true );

?>
<table class="form-table">
	<tr valign="top">
		<th scope="row">
			<label for="orbis_domain_name_ftp_keychain_id"><?php esc_html_e( 'FTP', 'orbis_keychains' ); ?></label>
		</th>
		<td>
			<input id="orbis_domain_name_ftp_keychain_id" name="_orbis_domain_name_ftp_keychain_id" value="<?php echo esc_attr( $ftp_keychain_id ); ?>" type="text" class="regular-text" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="orbis_domain_name_google_apps_keychain_id"><?php esc_html_e( 'Google Apps', 'orbis_keychains' ); ?></label>
		</th>
		<td>
			<input id="orbis_domain_name_google_apps_keychain_id" name="_orbis_domain_name_google_apps_keychain_id" value="<?php echo esc_attr( $google_apps_keychain_id ); ?>" type="text" class="regular-text" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="orbis_domain_name_wordpress_keychain_id"><?php esc_html_e( 'WordPress', 'orbis_keychains' ); ?></label>
		</th>
		<td>
			<input id="orbis_domain_name_wordpress_keychain_id" name="_orbis_domain_name_wordpress_keychain_id" value="<?php echo esc_attr( $wordpress_keychain_id ); ?>" type="text" class="regular-text" />
		</td>
	</tr>
</table>
