<?php
/*
Plugin Name: Orbis Keychains
Plugin URI: http://www.orbiswp.com/
Description: Give your whole team access to all the login details within your organization and keep a log of who used wich login details for what reason.

Version: 1.0.0
Requires at least: 3.5

Author: Pronamic
Author URI: http://www.pronamic.eu/

Text Domain: orbis_keychains
Domain Path: /languages/

License: Copyright (c) Pronamic

GitHub URI: https://github.com/pronamic/wp-orbis-keychains
*/

function orbis_keychains_bootstrap() {
	include 'classes/orbis-keychains-plugin.php';

	global $orbis_keychains_plugin;
	
	$orbis_keychains_plugin = new Orbis_Keychains_Plugin( __FILE__ );
}

add_action( 'orbis_bootstrap', 'orbis_keychains_bootstrap' );
