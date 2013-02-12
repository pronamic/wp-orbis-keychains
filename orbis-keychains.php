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

class Orbis_Keychains_Plugin {
	public $file;

	public function __construct( $file ) {
		$this->file    = $file;
		$this->dirname = dirname( $file );

		include $this->dirname . '/includes/post.php';
		include $this->dirname . '/includes/taxonomy.php';
		include $this->dirname . '/includes/hosting.php';
	}
}

global $orbis_keychains_plugin;

$orbis_keychains_plugin = new Orbis_Keychains_Plugin( __FILE__ );
