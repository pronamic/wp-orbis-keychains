<?php

/**
 * Add domain keychain meta boxes
 */
function orbis_domain_names_add_meta_boxes() {
	add_meta_box(
		'orbis_domain_name_keychains',
		__( 'Keychains', 'orbis_keychains' ),
		'orbis_domain_name_keychains_meta_box',
		'orbis_domain_name',
		'normal',
		'high'
	);

	add_meta_box(
		'orbis_hosting_group_keychains',
		__( 'Keychains', 'orbis_keychains' ),
		'orbis_hosting_group_keychains_meta_box',
		'orbis_hosting_group',
		'normal',
		'high'
	);
}

add_action( 'add_meta_boxes', 'orbis_domain_names_add_meta_boxes' );

/**
 * Domain name keychains meta box
 *
 * @param array $post
*/
function orbis_domain_name_keychains_meta_box( $post ) {
	global $orbis_keychains_plugin;

	$orbis_keychains_plugin->plugin_include( 'admin/meta-box-domain-name-keychains.php' );
}

/**
 * Hosting group keychains meta box
 *
 * @param array $post
*/
function orbis_hosting_group_keychains_meta_box( $post ) {
	global $orbis_keychains_plugin;

	$orbis_keychains_plugin->plugin_include( 'admin/meta-box-hosting-group-keychains.php' );
}

/**
 * Save domain name keychains
 */
function orbis_save_domain_name_keychains( $post_id, $post ) {
	// Doing autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Verify nonce
	$nonce = filter_input( INPUT_POST, 'orbis_domain_name_keychains_meta_box_nonce', FILTER_SANITIZE_STRING );
	if ( ! wp_verify_nonce( $nonce, 'orbis_save_domain_name_keychains' ) ) {
		return;
	}

	// Check permissions
	if ( ! ( $post->post_type == 'orbis_domain_name' && current_user_can( 'edit_post', $post_id ) ) ) {
		return;
	}

	// OK
	$definition = array(
		'_orbis_domain_name_ftp_keychain_id'         => FILTER_SANITIZE_STRING,
		'_orbis_domain_name_google_apps_keychain_id' => FILTER_SANITIZE_STRING,
		'_orbis_domain_name_wordpress_keychain_id'   => FILTER_SANITIZE_STRING
	);

	$data = filter_input_array( INPUT_POST, $definition );

	foreach ( $data as $key => $value ) {
		if ( empty( $value ) ) {
			delete_post_meta( $post_id, $key );
		} else {
			update_post_meta( $post_id, $key, $value );
		}
	}
}

add_action( 'save_post', 'orbis_save_domain_name_keychains', 10, 2 );

/**
 * Save hosting group keychains
 */
function orbis_save_hosting_group_keychains( $post_id, $post ) {
	// Doing autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Verify nonce
	$nonce = filter_input( INPUT_POST, 'orbis_hosting_group_keychains_meta_box_nonce', FILTER_SANITIZE_STRING );
	if ( ! wp_verify_nonce( $nonce, 'orbis_save_hosting_group_keychains' ) ) {
		return;
	}

	// Check permissions
	if ( ! ( $post->post_type == 'orbis_hosting_group' && current_user_can( 'edit_post', $post_id ) ) ) {
		return;
	}

	// OK
	$definition = array(
		'_orbis_hosting_group_control_panel_keychain_id' => FILTER_SANITIZE_STRING
	);

	$data = filter_input_array( INPUT_POST, $definition );

	foreach ( $data as $key => $value ) {
		if ( empty( $value ) ) {
			delete_post_meta( $post_id, $key );
		} else {
			update_post_meta( $post_id, $key, $value );
		}
	}
}

add_action( 'save_post', 'orbis_save_hosting_group_keychains', 10, 2 );

/**
 * Keychain content
*/
function orbis_domain_name_the_content( $content ) {
	if ( get_post_type() == 'orbis_domain_name' ) {
		$id = get_the_ID();

		$ftpKeychainId        = get_post_meta( $id, '_orbis_domain_name_ftp_keychain_id', true );
		$googleAppsKeychainId = get_post_meta( $id, '_orbis_domain_name_google_apps_keychain_id', true );
		$wordPressKeychainId  = get_post_meta( $id, '_orbis_domain_name_wordpress_keychain_id', true );

		$str  = '';

		$str .= '<h2>' . __( 'Keychains', 'orbis_keychains' ) . '</h2>';

		$str .= '<dl>';

		if ( ! empty( $ftpKeychainId ) ) {
			$str .= '	<dt>' . __( 'FTP', 'orbis_keychains' ) . '</dt>';
			$str .= '	<dd>' . sprintf( '<a href="%s">%s</a>', get_permalink( $ftpKeychainId ), get_the_title( $ftpKeychainId ) ) . '</dd>';
		}

		if ( ! empty( $googleAppsKeychainId ) ) {
			$str .= '	<dt>' . __( 'Google Apps', 'orbis_keychains' ) . '</dt>';
			$str .= '	<dd>' . sprintf( '<a href="%s">%s</a>', get_permalink( $googleAppsKeychainId ), get_the_title( $googleAppsKeychainId ) ) . '</dd>';
		}

		if ( ! empty( $wordPressKeychainId ) ) {
			$str .= '	<dt>' . __( 'WordPress', 'orbis_keychains' ) . '</dt>';
			$str .= '	<dd>' . sprintf( '<a href="%s">%s</a>', get_permalink( $wordPressKeychainId ), get_the_title( $wordPressKeychainId ) ) . '</dd>';
		}

		$str .= '</dl>';

		$content .= $str;
	}

	return $content;
}

add_filter( 'the_content', 'orbis_domain_name_the_content' );
