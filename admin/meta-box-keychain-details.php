<?php

global $post;

wp_nonce_field( 'orbis_save_keychain_details', 'orbis_keychain_details_meta_box_nonce' );

$url      = get_post_meta( $post->ID, '_orbis_keychain_url', true );
$username = get_post_meta( $post->ID, '_orbis_keychain_username', true );
$password = get_post_meta( $post->ID, '_orbis_keychain_password', true );
$email    = get_post_meta( $post->ID, '_orbis_keychain_email', true );

?>
<table class="form-table">
	<tr valign="top">
		<th scope="row">
			<label for="orbis_keychain_url"><?php esc_html_e( 'URL', 'orbis-keychains' ); ?></label>
		</th>
		<td>
			<input id="orbis_keychain_url" name="_orbis_keychain_url" value="<?php echo esc_attr( $url ); ?>" type="url" class="regular-text" autocomplete="off" data-lpignore="true" />
			<span class="description"><br /><?php echo wp_kses_post( __( 'Use an full URL: for HTTP <code>http://</code>, for FTP <code>ftp://</code>, for SFTP <code>sftp://</code>', 'orbis-keychains' ) ); ?></span>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="orbis_keychain_username"><?php esc_html_e( 'Username', 'orbis-keychains' ); ?></label>
		</th>
		<td>
			<input id="orbis_keychain_username" name="_orbis_keychain_username" value="<?php echo esc_attr( $username ); ?>" type="text" class="regular-text" autocomplete="off" data-lpignore="true" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="orbis_keychain_password"><?php esc_html_e( 'Password', 'orbis-keychains' ); ?></label>
		</th>
		<td>
			<input id="orbis_keychain_password" name="_orbis_keychain_password" value="<?php echo esc_attr( $password ); ?>" type="password" class="regular-text" autocomplete="new-password" data-lpignore="true" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="orbis_keychain_email"><?php esc_html_e( 'E-mail Address', 'orbis-keychains' ); ?></label>
		</th>
		<td>
			<input id="orbis_keychain_email" name="_orbis_keychain_email" value="<?php echo esc_attr( $email ); ?>" type="email" class="regular-text" autocomplete="off" data-lpignore="true" />
		</td>
	</tr>
</table>
