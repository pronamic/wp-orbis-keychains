<?php

/**
 * Add domain keychain meta boxes
 */
function orbis_domain_names_add_meta_boxes() {
	add_meta_box(
		'orbis_domain_name_keychains',
		__( 'Keychains', 'orbis-keychains' ),
		'orbis_domain_name_keychains_meta_box',
		'orbis_domain_name',
		'normal',
		'high'
	);

	add_meta_box(
		'orbis_hosting_group_keychains',
		__( 'Keychains', 'orbis-keychains' ),
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
	if ( ! ( 'orbis_domain_name' === $post->post_type && current_user_can( 'edit_post', $post_id ) ) ) {
		return;
	}

	// OK
	$definition = [
		'_orbis_domain_name_ftp_keychain_id'         => FILTER_SANITIZE_STRING,
		'_orbis_domain_name_google_apps_keychain_id' => FILTER_SANITIZE_STRING,
		'_orbis_domain_name_wordpress_keychain_id'   => FILTER_SANITIZE_STRING,
	];

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
	if ( ! ( 'orbis_hosting_group' === $post->post_type && current_user_can( 'edit_post', $post_id ) ) ) {
		return;
	}

	// OK
	$definition = [
		'_orbis_hosting_group_control_panel_keychain_id' => FILTER_SANITIZE_STRING,
	];

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
	if ( 'orbis_domain_name' === get_post_type() ) {
		$id = get_the_ID();

		$ftp_keychain_id         = get_post_meta( $id, '_orbis_domain_name_ftp_keychain_id', true );
		$google_apps_keychain_id = get_post_meta( $id, '_orbis_domain_name_google_apps_keychain_id', true );
		$wordpress_keychain_id   = get_post_meta( $id, '_orbis_domain_name_wordpress_keychain_id', true );

		$str = '';

		$str .= '<h2>' . __( 'Keychains', 'orbis-keychains' ) . '</h2>';

		$str .= '<dl>';

		if ( ! empty( $ftp_keychain_id ) ) {
			$str .= '	<dt>' . __( 'FTP', 'orbis-keychains' ) . '</dt>';
			$str .= '	<dd>' . sprintf( '<a href="%s">%s</a>', get_permalink( $ftp_keychain_id ), get_the_title( $ftp_keychain_id ) ) . '</dd>';
		}

		if ( ! empty( $google_apps_keychain_id ) ) {
			$str .= '	<dt>' . __( 'Google Apps', 'orbis-keychains' ) . '</dt>';
			$str .= '	<dd>' . sprintf( '<a href="%s">%s</a>', get_permalink( $google_apps_keychain_id ), get_the_title( $google_apps_keychain_id ) ) . '</dd>';
		}

		if ( ! empty( $wordpress_keychain_id ) ) {
			$str .= '	<dt>' . __( 'WordPress', 'orbis-keychains' ) . '</dt>';
			$str .= '	<dd>' . sprintf( '<a href="%s">%s</a>', get_permalink( $wordpress_keychain_id ), get_the_title( $wordpress_keychain_id ) ) . '</dd>';
		}

		$str .= '</dl>';

		$content .= $str;
	}

	return $content;
}

add_filter( 'the_content', 'orbis_domain_name_the_content' );
