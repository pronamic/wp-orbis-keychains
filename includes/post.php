<?php

function orbis_keychains_create_initial_post_types() {
	global $orbis_keychains_plugin;

	register_post_type(
		'orbis_keychain',
		array(
			'label'           => __( 'Keychains', 'orbis_keychains' ),
			'labels'          => array(
				'name'               => __( 'Keychains', 'orbis_keychains' ), 
				'singular_name'      => __( 'Keychain', 'orbis_keychains' ),
				'add_new'            => _x( 'Add New', 'orbis_keychain', 'orbis_keychains' ),
				'add_new_item'       => __( 'Add New Keychain', 'orbis_keychains' ),
				'edit_item'          => __( 'Edit Keychain', 'orbis_keychains' ),
				'new_item'           => __( 'New Keychain', 'orbis_keychains' ),
				'view_item'          => __( 'View Keychain', 'orbis_keychains' ),
				'search_items'       => __( 'Search Keychains', 'orbis_keychains' ),
				'not_found'          => __( 'No keychains found', 'orbis_keychains' ),
				'not_found_in_trash' => __( 'No keychains found in Trash', 'orbis_keychains' ) 
			) ,
			'public'          => true,
			'menu_position'   => 30,
			'menu_icon'       => $orbis_keychains_plugin->plugin_url( 'admin/images/keychain.png' ),
			'capability_type' => array( 'keychain', 'keychains' ),
			'supports'        => array( 'title', 'editor', 'author', 'comments' ),
			'has_archive'     => true,
			'rewrite'         => array( 'slug' => _x( 'keychains', 'slug', 'orbis_keychains' ) ) 
		)
	);
}

add_action( 'init', 'orbis_keychains_create_initial_post_types', 0 ); // highest priority

function orbis_keychain_setup_roles() {
	$default_capabilities = array(
		'edit_keychain'              => true,
		'read_keychain'              => true,
		'delete_keychain'            => true,
		'edit_keychains'             => true,
		'edit_others_keychains'      => true,
		'publish_keychains'          => true,
		'read_private_keychains'     => true,
		'delete_keychains'           => true,
		'delete_private_keychains'   => true,
		'delete_published_keychains' => true,
		'delete_other_keychains'     => true,
		'edit_private_keychains'     => true,
		'edit_published_keychains'   => true
	);

	$roles = array(
		'administrator' => array(
			'display_name' => __( 'Administrator', 'orbis_keychains' ),
			'capabilities' => array_merge( $default_capabilities, array(

			) )
		) ,
		'editor' => array(
			'display_name' => __( 'Editor', 'orbis_keychains' ),
			'capabilities' => array_merge( $default_capabilities, array(
				'edit_others_keychains' => false
			) )
		)
	);

	orbis_update_roles( $roles );
}

function orbis_update_roles( $roles ) {
	global $wp_roles;

	if ( ! isset( $wp_roles ) ) {
		$wp_roles = new WP_Roles();
	}

	foreach ( $roles as $role => $data ) {
		if ( isset( $data['display_name'], $data['capabilities'] ) ) {
			$display_name = $data['display_name'];
			$capabilities = $data['capabilities'];

			if ( $wp_roles->is_role( $role ) ) {
				foreach  ( $capabilities as $cap => $grant ) {
					$wp_roles->add_cap( $role, $cap, $grant );
				}
			} else {
				$wp_roles->add_role( $role, $display_name, $capabilities );
			}
		}
	}
}

/**
 * Add keychain meta boxes
 */
function orbis_keychain_add_meta_boxes() {
	add_meta_box(
		'orbis_keychain',
		__( 'Keychain Details', 'orbis_keychains' ),
		'orbis_keychain_details_meta_box',
		'orbis_keychain',
		'normal',
		'high'
	);
}

add_action( 'add_meta_boxes', 'orbis_keychain_add_meta_boxes' );

/**
 * Keychain details meta box
 *
 * @param array $post
*/
function orbis_keychain_details_meta_box( $post ) {
	global $orbis_keychains_plugin;

	include dirname( $orbis_keychains_plugin->file) . '/admin/meta-box-keychain-details.php';
}

/**
 * Save keychain details
 */
function orbis_save_keychain_details( $post_id, $post ) {
	// Doing autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Verify nonce
	$nonce = filter_input( INPUT_POST, 'orbis_keychain_details_meta_box_nonce', FILTER_SANITIZE_STRING );
	if ( ! wp_verify_nonce( $nonce, 'orbis_save_keychain_details' ) ) {
		return;
	}

	// Check permissions
	if ( ! ( $post->post_type == 'orbis_keychain' && current_user_can( 'edit_post', $post_id ) ) ) {
		return;
	}

	// OK
	$currentPassword = get_post_meta( $post_id, '_orbis_keychain_password', true );
	$newPassword     = filter_input( INPUT_POST, '_orbis_keychain_password', FILTER_SANITIZE_STRING );

	$definition = array(
		'_orbis_keychain_url'      => FILTER_VALIDATE_URL,
		'_orbis_keychain_email'    => FILTER_VALIDATE_EMAIL,
		'_orbis_keychain_username' => FILTER_SANITIZE_STRING,
		'_orbis_keychain_password' => FILTER_SANITIZE_STRING
	);

	$data = filter_input_array(INPUT_POST, $definition);

	foreach ( $data as $key => $value ) {
		if ( empty( $value ) ) {
			delete_post_meta( $post_id, $key );
		} else {
			update_post_meta( $post_id, $key, $value );
		}
	}

	if ( ! empty( $currentPassword ) && $currentPassword != $newPassword ) {
		$user = wp_get_current_user();

		$data = array(
			'comment_post_ID'      => $post_id,
			'comment_content'      => __( 'I changed the password of this keychain.', 'orbis_keychains' ),
			'comment_author'       => $user->display_name,
			'comment_author_email' => $user->user_email,
			'comment_author_url'   => $user->user_url,
			'user_id'              => $user->ID
		);

		$comment_ID = wp_new_comment( $data );
	}
}

add_action( 'save_post', 'orbis_save_keychain_details', 10, 2 );

/**
 * Keychian password required word count
 *
 * @return int
*/
function orbis_keychain_get_password_required_word_count() {
	return 10;
}

/**
 * Comment form defaults
 */
function orbis_keychain_comment_form($post_id) {
	// Some themes call this function, don't show the checkbox again
	remove_action( 'comment_form', __FUNCTION__ );

	if ( get_post_type( $post_id ) == 'orbis_keychain' ) {
		$str  = '';

		$str .= '<div class="checkbox">';
		$str .=	'	<label>';
		$str .= '		<input type="checkbox" name="orbis_keychain_password_request" value="true" /> ';
		$str .= '		' . sprintf( __( 'Request password, describe with at least <strong>%d words</strong> why you need this password.', 'orbis_keychains' ), orbis_keychain_get_password_required_word_count() );
		$str .= '	</label>';
		$str .= '</div>';

		echo $str;
	}
}

add_filter( 'comment_form', 'orbis_keychain_comment_form' );

/**
 * Keychain comment post
 *
 * @param string $comment_id
 * @param string $approved
*/
function orbis_keychain_comment_post( $comment_id, $approved ) {
	$is_password_request = filter_input( INPUT_POST, 'orbis_keychain_password_request', FILTER_VALIDATE_BOOLEAN );

	if ( $is_password_request ) {
		add_comment_meta( $comment_id, 'orbis_keychain_password_request', $is_password_request, true );
	}
}

add_action( 'comment_post', 'orbis_keychain_comment_post', 50, 2 );

/**
 * Keychain comment text
*/
function orbis_keychain_get_comment_text($text, $comment) {
	$isPasswordRequest = get_comment_meta($comment->comment_ID, 'orbis_keychain_password_request', true);

	if($isPasswordRequest) {
		$currentDate = new DateTime();

		$visibleTillDate = new DateTime($comment->comment_date);
		$visibleTillDate->modify('+2 days');

		$str  = '';

		$str .= '<div style="font-style: italic;">';

		$currentUser = wp_get_current_user();
		$isCurrentUser = $currentUser->ID == $comment->user_id;

		$wordCount = str_word_count($comment->comment_content);
		$wordCountRequired = orbis_keychain_get_password_required_word_count();
		$isCommentEnough = $wordCount >= $wordCountRequired;

		$isWithinDate = $visibleTillDate->format('U') > $currentDate->format('U');

		if ( $isCommentEnough ) {
			$str .= '<p>';
			$str .= '	' . sprintf(
				__( 'This comment was an password request, the user can view the password till <strong>%s</strong>.', 'orbis_keychains' ),
				$visibleTillDate->format( DATE_W3C )
			);
			$str .= '</p>';
		} else {
			$str .= '<p>';
			$str .= '	' . sprintf(
				__( 'This comment was met <strong>%d words</strong> not long enough to display the password, use at least <strong>%d words</strong>.', 'orbis_keychains' ),
				$wordCount,
				$wordCountRequired
			);
			$str .= '</p>';
		}

		if ( $isCurrentUser && $isCommentEnough && $isWithinDate ) {
			$url      = get_post_meta($comment->comment_post_ID, '_orbis_keychain_url', true);
			$username = get_post_meta($comment->comment_post_ID, '_orbis_keychain_username', true);
			$password = get_post_meta($comment->comment_post_ID, '_orbis_keychain_password', true);
			$email    = get_post_meta($comment->comment_post_ID, '_orbis_keychain_email', true);

			$url_full = http_build_url( $url, array(
				'user' => $username,
				'pass' => $password
			) );

			$str .= '<dl>';

			$str .= '	<dt>' . sprintf( '<label for="url-full-field-%d">%s</label>', $comment->comment_ID, __( 'URL Full', 'orbis_keychains' ) ) . '</dt>';
			$str .= '	<dd>' . sprintf( '<a href="%s">%s</a>', $url_full, $url_full ) . '</dd>';
			$str .= '	<dd>' . sprintf( '<input id="url-full-field-%d" class="form-control" type="text" value="%s" readonly="readonly" />', $comment->comment_ID, esc_attr( $url_full ) ) . '</dd>';

			$str .= '	<dt>' . sprintf( '<label for="url-field-%d">%s</label>', $comment->comment_ID, __( 'URL', 'orbis_keychains' ) ) . '</dt>';
			$str .= '	<dd>' . sprintf( '<a href="%s">%s</a>', $url, $url) . '</dd>';
			$str .= '	<dd>' . sprintf( '<input id="url-field-%d" class="form-control" type="text" value="%s" readonly="readonly" />', $comment->comment_ID, esc_attr( $url ) ) . '</dd>';

			$str .= '	<dt>' . sprintf( '<label for="username-field-%d">%s</label>', $comment->comment_ID, __( 'Username', 'orbis_keychains' ) ) . '</dt>';
			$str .= '	<dd>' . sprintf( '<input id="username-field-%d" class="form-control" type="text" value="%s" readonly="readonly" />', $comment->comment_ID, esc_attr( $username ) ) . '</dd>';

			$str .= '	<dt>' . sprintf( '<label for="password-field-%d">%s</label>', $comment->comment_ID, __( 'Password', 'orbis_keychains' ) ) . '</dt>';
			$str .= '	<dd>' . sprintf( '<input id="password-field-%d" class="form-control" type="text" value="%s" readonly="readonly" />', $comment->comment_ID, esc_attr( $password ) ) . '</dd>';

			if ( ! empty( $email ) ) {
				$str .= '	<dt>' . sprintf( '<label for="email-field-%d">%s</label>', $comment->comment_ID, __( 'E-mail Address', 'orbis_keychains' ) ) . '</dt>';
				$str .= '	<dd>' . sprintf( '<input id="email-field-%d" class="form-control" type="text" value="%s" readonly="readonly" />', $comment->comment_ID, esc_attr( $email ) ) . '</dd>';
			}

			$str .= '</dl>';
		}

		$str .= '</div>';

		$text .= $str;
	}

	return $text;
}

add_filter( 'comment_text', 'orbis_keychain_get_comment_text', 20, 2 );

/**
 * Keychain content
*/
function orbis_keychain_the_content( $content ) {
	if ( get_post_type() == 'orbis_keychain' ) {
		$id = get_the_ID();

		$url      = get_post_meta( $id, '_orbis_keychain_url', true );
		$email    = get_post_meta( $id, '_orbis_keychain_email', true );
		$username = get_post_meta( $id, '_orbis_keychain_username', true );
		$password = get_post_meta( $id, '_orbis_keychain_password', true );

		$str  = '';

		$str .= '<dl>';

		$str .= '	<dt>' . __( 'URL', 'orbis_keychains' ) . '</dt>';
		$str .= '	<dd>' . sprintf( '<a href="%s">%s</a>', $url, $url ) . '</dd>';

		$str .= '	<dt>' . __( 'Username', 'orbis_keychains' ) . '</dt>';
		$str .= '	<dd>' . sprintf( '<input type="text" class="form-control" value="%s" readonly="readonly" />', esc_attr( $username ) ) . '</dd>';

		$str .= '	<dt>' . __( 'Password', 'orbis_keychains' ) . '</dt>';
		$str .= '	<dd>' . '********' . '</dd>';

		if ( ! empty( $email ) ) {
			$str .= '	<dt>' . __( 'E-mail Address', 'orbis_keychains' ) . '</dt>';
			$str .= '	<dd>' . sprintf( '<a href="mailto:%s">%s</a>', $email, $email ) . '</dd>';
		}

		$str .= '</dl>';
		
		if ( has_term( 'WordPress', 'orbis_keychain_category' ) ) {
			$str .= sprintf( '<form method="post" action="%s" target="_blank">', esc_attr( $url ) );
			$str .= sprintf( '<input type="hidden" value="%s" name="log" />', esc_attr( $username ) );
			$str .= sprintf( '<input type="hidden" value="%s" name="pwd" />', esc_attr( $password ) );
			$str .= sprintf( '<input type="submit" value="%s" name="wp-submit" />', esc_attr( __( 'Login', 'orbis_keychains' ) ) );
			$str .= sprintf( '</form>' );
		}

		$content .= $str;
	}

	return $content;
}

add_filter( 'the_content', 'orbis_keychain_the_content' );

/**
 * Keychain edit columns
*/
function orbis_keychain_edit_columns( $columns ) {
	return array(
		'cb'                        => '<input type="checkbox" />',
		'title'                     => __( 'Title', 'orbis_keychains' ),
		'orbis_keychain_url'        => __( 'URL', 'orbis_keychains' ),
		'orbis_keychain_username'   => __( 'Username', 'orbis_keychains' ),
		'orbis_keychain_email'      => __( 'E-mail Address', 'orbis_keychains' ),
		'author'                    => __( 'Author', 'orbis_keychains' ),
		'orbis_keychain_categories' => __( 'Categories', 'orbis_keychains' ),
		'orbis_keychain_tags'       => __( 'Tags', 'orbis_keychains' ),
		'comments'                  => __( 'Comments', 'orbis_keychains' ),
		'date'                      => __( 'Date', 'orbis_keychains' )
	);
}

add_filter( 'manage_edit-orbis_keychain_columns' , 'orbis_keychain_edit_columns' );

/**
 * Keychain column
 *
 * @param string $column
*/
function orbis_keychain_column( $column ) {
	$id = get_the_ID();

	switch ( $column ) {
		case 'orbis_keychain_url' :
			$url = get_post_meta( $id, '_orbis_keychain_url', true );

			if ( ! empty( $url ) ) {
				printf( '<a href="%s" target="_blank">%s</a>', $url, $url );
			}

			break;
		case 'orbis_keychain_username' :
			echo get_post_meta( $id, '_orbis_keychain_username', true );

			break;
		case 'orbis_keychain_email' :
			echo get_post_meta( $id, '_orbis_keychain_email', true );

			break;
		case 'orbis_keychain_categories' :
			echo get_the_term_list( $id, 'orbis_keychain_category' , '' , ', ' , '' );

			break;
		case 'orbis_keychain_tags' :
			echo get_the_term_list( $id, 'orbis_keychain_tag' , '' , ', ' , '' );

			break;
	}
}

add_action( 'manage_posts_custom_column' , 'orbis_keychain_column' );
