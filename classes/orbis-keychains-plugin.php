<?php
/**
 * Orbis Keychains plugin.
 *
 * @package Orbis_Keychains
 */

/**
 * Orbis Keychains plugin.
 */
class Orbis_Keychains_Plugin extends Orbis_Plugin {
	/**
	 * Admin.
	 *
	 * @var Orbis_Keychains_Admin
	 */
	private $admin;

	/**
	 * Construct and initialize the plugin.
	 *
	 * @param string $file Plugin file.
	 */
	public function __construct( $file ) {
		parent::__construct( $file );

		$this->plugin_include( 'includes/hosting.php' );
		$this->plugin_include( 'includes/http_build_url.php' );
		$this->plugin_include( 'includes/post.php' );
		$this->plugin_include( 'includes/taxonomy.php' );
		$this->plugin_include( 'includes/template.php' );

		// Admin.
		if ( is_admin() ) {
			$this->admin = new Orbis_Keychains_Admin( $this );
		}
	}

	/**
	 * Load plugin text domain.
	 */
	public function loaded() {
		$this->load_textdomain( 'orbis_keychains', '/languages/' );
	}

	/**
	 * Install plugin.
	 */
	public function install() {
		orbis_keychain_setup_roles();

		parent::install();
	}
}
