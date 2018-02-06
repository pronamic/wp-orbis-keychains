<?php

function orbis_keychains_create_initial_taxonomies() {
	global $orbis_keychains_plugin;

	register_taxonomy(
		'orbis_keychain_category',
		array( 'orbis_keychain' ),
		array(
			'hierarchical' => true,
			'labels'       => array(
				'name'              => _x( 'Categories', 'orbis_keychain_category', 'orbis_keychains' ),
				'singular_name'     => _x( 'Category', 'orbis_keychain_category', 'orbis_keychains' ),
				'search_items'      => __( 'Search Categories', 'orbis_keychains' ),
				'all_items'         => __( 'All Categories', 'orbis_keychains' ),
				'parent_item'       => __( 'Parent Category', 'orbis_keychains' ),
				'parent_item_colon' => __( 'Parent Category:', 'orbis_keychains' ),
				'edit_item'         => __( 'Edit Category', 'orbis_keychains' ),
				'update_item'       => __( 'Update Category', 'orbis_keychains' ),
				'add_new_item'      => __( 'Add New Category', 'orbis_keychains' ),
				'new_item_name'     => __( 'New Category Name', 'orbis_keychains' ),
				'menu_name'         => __( 'Categories', 'orbis_keychains' ),
			),
			'show_ui'      => true,
			'query_var'    => true,
			'rewrite'      => array(
				'slug' => _x( 'keychain-categorie', 'slug', 'orbis_keychains' ),
			),
		)
	);

	register_taxonomy(
		'orbis_keychain_tag',
		array( 'orbis_keychain' ),
		array(
			'hierarchical' => false,
			'labels'       => array(
				'name'              => _x( 'Tags', 'orbis_keychain_category', 'orbis_keychains' ),
				'singular_name'     => _x( 'Tag', 'orbis_keychain_category', 'orbis_keychains' ),
				'search_items'      => __( 'Search Tags', 'orbis_keychains' ),
				'all_items'         => __( 'All Tags', 'orbis_keychains' ),
				'parent_item'       => __( 'Parent Tag', 'orbis_keychains' ),
				'parent_item_colon' => __( 'Parent Tag:', 'orbis_keychains' ),
				'edit_item'         => __( 'Edit Tag', 'orbis_keychains' ),
				'update_item'       => __( 'Update Tag', 'orbis_keychains' ),
				'add_new_item'      => __( 'Add New Tag', 'orbis_keychains' ),
				'new_item_name'     => __( 'New Tag Name', 'orbis_keychains' ),
				'menu_name'         => __( 'Tags', 'orbis_keychains' ),
			),
			'show_ui'      => true,
			'query_var'    => true,
			'rewrite'      => array(
				'slug' => _x( 'keychain-tag', 'slug', 'orbis_keychains' ),
			),
		)
	);
}

add_action( 'init', 'orbis_keychains_create_initial_taxonomies', 0 ); // highest priority
