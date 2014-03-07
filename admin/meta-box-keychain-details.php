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
			<label for="orbis_keychain_url"><?php _e( 'URL', 'orbis_keychains' ); ?></label>
		</th>
		<td>
			<input id="orbis_keychain_url" name="_orbis_keychain_url" value="<?php echo esc_attr( $url ); ?>" type="url" class="regular-text" />
			<span class="description"><br /><?php _e( 'Use an full URL: for HTTP <code>http://</code>, for FTP <code>ftp://</code>, for SFTP <code>sftp://</code>', 'orbis_keychains' ); ?></span>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="orbis_keychain_username"><?php _e( 'Username', 'orbis_keychains' ); ?></label>
		</th>
		<td>
			<input id="orbis_keychain_username" name="_orbis_keychain_username" value="<?php echo esc_attr( $username ); ?>" type="text" class="regular-text" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="orbis_keychain_password"><?php _e( 'Password', 'orbis_keychains' ); ?></label>
		</th>
		<td>
			<input id="orbis_keychain_password" name="_orbis_keychain_password" value="<?php echo esc_attr( $password ); ?>" type="password" class="regular-text" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="orbis_keychain_email"><?php _e( 'E-mail Address', 'orbis_keychains' ); ?></label>
		</th>
		<td>
			<input id="orbis_keychain_email" name="_orbis_keychain_email" value="<?php echo esc_attr( $email ); ?>" type="email" class="regular-text" />
		</td>
	</tr>
</table>