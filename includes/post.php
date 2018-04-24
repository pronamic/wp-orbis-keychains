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
				'not_found_in_trash' => __( 'No keychains found in Trash', 'orbis_keychains' ),
			),
			'public'          => true,
			'menu_position'   => 30,
			'menu_icon'       => 'dashicons-admin-network',
			'capability_type' => array( 'keychain', 'keychains' ),
			'supports'        => array( 'title', 'editor', 'author', 'comments' ),
			'has_archive'     => true,
			'rewrite'         => array( 'slug' => _x( 'keychains', 'slug', 'orbis_keychains' ) ),
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
		'edit_published_keychains'   => true,
	);

	$roles = array(
		'administrator' => array(
			'display_name' => __( 'Administrator', 'orbis_keychains' ),
			'capabilities' => array_merge( $default_capabilities, array() ),
		),
		'editor' => array(
			'display_name' => __( 'Editor', 'orbis_keychains' ),
			'capabilities' => array_merge( $default_capabilities, array(
				'edit_others_keychains' => false,
			) ),
		),
	);

	orbis_update_roles( $roles );
}

function orbis_update_roles( $roles ) {
	global $wp_roles;

	foreach ( $roles as $role => $data ) {
		if ( isset( $data['display_name'], $data['capabilities'] ) ) {
			$display_name = $data['display_name'];
			$capabilities = $data['capabilities'];

			if ( $wp_roles->is_role( $role ) ) {
				foreach ( $capabilities as $cap => $grant ) {
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

	include dirname( $orbis_keychains_plugin->file ) . '/admin/meta-box-keychain-details.php';
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
	if ( ! ( 'orbis_keychain' === $post->post_type && current_user_can( 'edit_post', $post_id ) ) ) {
		return;
	}

	// OK
	$definition = array(
		'_orbis_keychain_url'      => FILTER_VALIDATE_URL,
		'_orbis_keychain_email'    => FILTER_VALIDATE_EMAIL,
		'_orbis_keychain_username' => FILTER_SANITIZE_STRING,
		'_orbis_keychain_password' => FILTER_UNSAFE_RAW,
		'_orbis_keychain_username' => FILTER_SANITIZE_STRING,
		'_orbis_keychain_path'     => FILTER_SANITIZE_STRING,
		'_orbis_keychain_port'     => FILTER_SANITIZE_STRING,
		'_orbis_keychain_has_cli'  => FILTER_VALIDATE_BOOLEAN,
	);

	$data = wp_slash( filter_input_array( INPUT_POST, $definition ) );

	// Pasword
	$password_old = get_post_meta( $post_id, '_orbis_keychain_password', true );
	$password_new = $data['_orbis_keychain_password'];

	foreach ( $data as $key => $value ) {
		if ( empty( $value ) ) {
			delete_post_meta( $post_id, $key );
		} else {
			update_post_meta( $post_id, $key, $value );
		}
	}

	// Action
	if ( 'publish' === $post->post_status && ! empty( $password_old ) && $password_old !== $password_new ) {
		// @see https://github.com/woothemes/woocommerce/blob/v2.1.4/includes/class-wc-order.php#L1274
		do_action( 'orbis_keychain_password_update', $post_id, $password_old, $password_new );
	}
}

add_action( 'save_post', 'orbis_save_keychain_details', 10, 2 );

/**
 * Keychain password update
 *
 * @param int $post_id
 */
function orbis_keychain_password_update( $post_id, $password_old, $password_new ) {
	$user = wp_get_current_user();

	$comment_content = sprintf(
		__( "The password '%s' was changed to '%s' by %s.", 'orbis_keychains' ),
		str_repeat( '*', strlen( $password_old ) ),
		str_repeat( '*', strlen( $password_new ) ),
		$user->display_name
	);

	$data = array(
		'comment_post_ID' => $post_id,
		'comment_content' => $comment_content,
		'comment_author'  => 'Orbis',
		'comment_type'    => 'orbis_comment',
	);

	$comment_id = wp_insert_comment( $data );
}

add_action( 'orbis_keychain_password_update', 'orbis_keychain_password_update', 10, 3 );

/**
 * Keychian password required word count
 *
 * @return int
*/
function orbis_keychain_get_password_required_word_count() {
	return intval( get_option( 'orbis_keychains_word_count', 10 ) );
}

/**
 * Comment form defaults
 */
function orbis_keychain_comment_form( $post_id ) {
	// Some themes call this function, don't show the checkbox again
	remove_action( 'comment_form', __FUNCTION__ );

	if ( 'orbis_keychain' === get_post_type( $post_id ) ) : ?>

		<div class="checkbox">
			<label>
				<input type="checkbox" name="orbis_keychain_password_request" value="true" />

				<?php

				printf(
					__( 'Request password, describe with at least <strong>%d words</strong> why you need this password.', 'orbis_keychains' ),
					orbis_keychain_get_password_required_word_count()
				);

				?>
			</label>
		</div>

	<?php endif;
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
function orbis_keychain_get_comment_text( $text, $comment ) {
	$is_password_request = get_comment_meta( $comment->comment_ID, 'orbis_keychain_password_request', true );

	if ( $is_password_request ) {
		$current_date = new DateTime();

		$visible_till_date = new DateTime( $comment->comment_date );
		$visible_till_date->modify( '+2 days' );

		$str  = '';

		$str .= '<div style="font-style: italic;">';

		$current_user = wp_get_current_user();
		$is_current_user = $current_user->ID === $comment->user_id;

		$word_count = str_word_count( $comment->comment_content );
		$word_count_required = orbis_keychain_get_password_required_word_count();
		$is_comment_enough = $word_count >= $word_count_required;

		$is_within_date = $visible_till_date->format( 'U' ) > $current_date->format( 'U' );

		if ( $is_comment_enough ) {
			$str .= '<p>';
			$str .= '	' . sprintf(
				__( 'This comment was an password request, the user can view the password till <strong>%s</strong>.', 'orbis_keychains' ),
				$visible_till_date->format( DATE_W3C )
			);
			$str .= '</p>';
		} else {
			$str .= '<p>';
			$str .= '	' . sprintf(
				__( 'This comment was met <strong>%d words</strong> not long enough to display the password, use at least <strong>%d words</strong>.', 'orbis_keychains' ),
				$word_count,
				$word_count_required
			);
			$str .= '</p>';
		}

		if ( $is_current_user && $is_comment_enough && $is_within_date ) {
			$url      = get_post_meta( $comment->comment_post_ID, '_orbis_keychain_url', true );
			$username = get_post_meta( $comment->comment_post_ID, '_orbis_keychain_username', true );
			$password = get_post_meta( $comment->comment_post_ID, '_orbis_keychain_password', true );
			$email    = get_post_meta( $comment->comment_post_ID, '_orbis_keychain_email', true );

			$url_full = http_build_url( $url, array(
				'user' => $username,
				'pass' => $password,
			) );

			$str .= '<dl>';

			$str .= '	<dt>' . sprintf( '<label for="url-full-field-%d">%s</label>', $comment->comment_ID, __( 'URL Full', 'orbis_keychains' ) ) . '</dt>';
			$str .= '	<dd>' . sprintf( '<a href="%s">%s</a>', $url_full, $url_full ) . '</dd>';
			$str .= '	<dd>' . sprintf( '<input id="url-full-field-%d" class="form-control" type="text" value="%s" readonly="readonly" />', $comment->comment_ID, esc_attr( $url_full ) ) . '</dd>';

			$str .= '	<dt>' . sprintf( '<label for="url-field-%d">%s</label>', $comment->comment_ID, __( 'URL', 'orbis_keychains' ) ) . '</dt>';
			$str .= '	<dd>' . sprintf( '<a href="%s">%s</a>', $url, $url ) . '</dd>';
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
	if ( 'orbis_keychain' === get_post_type() ) {
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
		$str .= '	<dd>********</dd>';

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
		'date'                      => __( 'Date', 'orbis_keychains' ),
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
				printf( '<a href="%s" target="_blank">%s</a>', esc_attr( $url ), esc_html( $url ) );
			}

			break;
		case 'orbis_keychain_username' :
			echo esc_html( get_post_meta( $id, '_orbis_keychain_username', true ) );

			break;
		case 'orbis_keychain_email' :
			echo esc_html( get_post_meta( $id, '_orbis_keychain_email', true ) );

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
