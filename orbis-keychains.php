<?php
/*
Plugin Name: Orbis Keychains
Plugin URI: http://orbiswp.com/
Description: 

Version: 0.1
Requires at least: 3.5

Author: Pronamic
Author URI: http://pronamic.eu/

Text Domain: orbis
Domain Path: /languages/

License: GPL

GitHub URI: https://github.com/pronamic/wp-orbis-keychains
*/

function orbis_keychains_bootstrap() {
	include 'classes/orbis-keychains-plugin.php';

	global $orbis_keychains_plugin;
	
	$orbis_keychains_plugin = new Orbis_Keychains_Plugin( __FILE__ );
}

add_action( 'orbis_bootstrap', 'orbis_keychains_bootstrap' );
