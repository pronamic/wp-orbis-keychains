<?php

function orbis_keychains_create_initial_taxonomies() {
	global $orbis_keychains_plugin;

	register_taxonomy(
		'orbis_keychain_category',
		[ 'orbis_keychain' ],
		[
			'hierarchical' => true,
			'labels'       => [
				'name'              => _x( 'Categories', 'orbis_keychain_category', 'orbis-keychains' ),
				'singular_name'     => _x( 'Category', 'orbis_keychain_category', 'orbis-keychains' ),
				'search_items'      => __( 'Search Categories', 'orbis-keychains' ),
				'all_items'         => __( 'All Categories', 'orbis-keychains' ),
				'parent_item'       => __( 'Parent Category', 'orbis-keychains' ),
				'parent_item_colon' => __( 'Parent Category:', 'orbis-keychains' ),
				'edit_item'         => __( 'Edit Category', 'orbis-keychains' ),
				'update_item'       => __( 'Update Category', 'orbis-keychains' ),
				'add_new_item'      => __( 'Add New Category', 'orbis-keychains' ),
				'new_item_name'     => __( 'New Category Name', 'orbis-keychains' ),
				'menu_name'         => __( 'Categories', 'orbis-keychains' ),
			],
			'show_ui'      => true,
			'query_var'    => true,
			'rewrite'      => [
				'slug' => _x( 'keychain-categorie', 'slug', 'orbis-keychains' ),
			],
		]
	);

	register_taxonomy(
		'orbis_keychain_tag',
		[ 'orbis_keychain' ],
		[
			'hierarchical' => false,
			'labels'       => [
				'name'              => _x( 'Tags', 'orbis_keychain_category', 'orbis-keychains' ),
				'singular_name'     => _x( 'Tag', 'orbis_keychain_category', 'orbis-keychains' ),
				'search_items'      => __( 'Search Tags', 'orbis-keychains' ),
				'all_items'         => __( 'All Tags', 'orbis-keychains' ),
				'parent_item'       => __( 'Parent Tag', 'orbis-keychains' ),
				'parent_item_colon' => __( 'Parent Tag:', 'orbis-keychains' ),
				'edit_item'         => __( 'Edit Tag', 'orbis-keychains' ),
				'update_item'       => __( 'Update Tag', 'orbis-keychains' ),
				'add_new_item'      => __( 'Add New Tag', 'orbis-keychains' ),
				'new_item_name'     => __( 'New Tag Name', 'orbis-keychains' ),
				'menu_name'         => __( 'Tags', 'orbis-keychains' ),
			],
			'show_ui'      => true,
			'query_var'    => true,
			'rewrite'      => [
				'slug' => _x( 'keychain-tag', 'slug', 'orbis-keychains' ),
			],
		]
	);
}

add_action( 'init', 'orbis_keychains_create_initial_taxonomies', 0 ); // highest priority
