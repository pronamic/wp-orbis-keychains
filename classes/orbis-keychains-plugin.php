<?php

class Orbis_Keychains_Plugin extends Orbis_Plugin {
	public function __construct( $file ) {
		parent::__construct( $file );

		$this->plugin_include( 'includes/hosting.php' );
		$this->plugin_include( 'includes/http_build_url.php' );
		$this->plugin_include( 'includes/post.php' );
		$this->plugin_include( 'includes/taxonomy.php' );
	}

	public function loaded() {
		$this->load_textdomain( 'orbis_keychains', '/languages/' );
	}
}
