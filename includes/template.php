<?php

function orbis_hosting_group_keychains_details() {
	if ( is_singular( 'orbis_hosting_group' ) ) {
		global $orbis_keychains_plugin;

		$orbis_keychains_plugin->plugin_include( 'templates/hosting-group-keychains.php' );
	}
}

add_action( 'orbis_after_side_content', 'orbis_hosting_group_keychains_details' );
