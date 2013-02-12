<?php

function orbis_keychains_create_initial_taxonomies() {
	global $orbis_keychains_plugin;

	register_taxonomy(
		'orbis_keychain_category',
		array( 'orbis_keychain' ),
		array(
			'hierarchical' => true,
			'labels'       => array(
				'name'              => _x( 'Categories', 'orbis_keychain_category', 'orbis' ),
				'singular_name'     => _x( 'Category', 'orbis_keychain_category', 'orbis' ),
				'search_items'      => __( 'Search Categories', 'orbis' ),
				'all_items'         => __( 'All Categories', 'orbis' ),
				'parent_item'       => __( 'Parent Category', 'orbis' ),
				'parent_item_colon' => __( 'Parent Category:', 'orbis' ),
				'edit_item'         => __( 'Edit Category', 'orbis' ),
				'update_item'       => __( 'Update Category', 'orbis' ),
				'add_new_item'      => __( 'Add New Category', 'orbis' ),
				'new_item_name'     => __( 'New Category Name', 'orbis' ),
				'menu_name'         => __( 'Categories', 'orbis' )
			),
			'show_ui'      => true, 
			'query_var'    => true, 
			'rewrite'      => array(
				'slug' => _x( 'keychain-categorie', 'slug', 'orbis' )
			)
		)
	);

	register_taxonomy(
		'orbis_keychain_tag',
		array( 'orbis_keychain' ),
		array(
			'hierarchical' => false,
			'labels'       => array(
				'name'              => _x( 'Tags', 'orbis_keychain_category', 'orbis'),
				'singular_name'     => _x( 'Tag', 'orbis_keychain_category', 'orbis'),
				'search_items'      => __( 'Search Tags', 'orbis' ),
				'all_items'         => __( 'All Tags', 'orbis' ),
				'parent_item'       => __( 'Parent Tag', 'orbis' ),
				'parent_item_colon' => __( 'Parent Tag:', 'orbis' ),
				'edit_item'         => __( 'Edit Tag', 'orbis' ),
				'update_item'       => __( 'Update Tag', 'orbis' ),
				'add_new_item'      => __( 'Add New Tag', 'orbis' ),
				'new_item_name'     => __( 'New Tag Name', 'orbis' ),
				'menu_name'         => __( 'Tags', 'orbis' )
			),
			'show_ui'      => true,
			'query_var'    => true,
			'rewrite'      => array(
				'slug' => _x( 'keychain-tag', 'slug', 'orbis' )
			)
		)
	);
}

add_action( 'init', 'orbis_keychains_create_initial_taxonomies', 0 ); // highest priority
