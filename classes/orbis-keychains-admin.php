<?php

/**
 * Title: Orbis Keychains admin
 * Description:
 * Copyright: Copyright (c) 2005 - 2014
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.0.0
 */
class Orbis_Keychains_Admin {
	/**
	 * Plugin
	 *
	 * @var Orbis_Twitter_Plugin
	 */
	private $plugin;

	//////////////////////////////////////////////////

	/**
	 * Constructs and initialize an Orbis core admin
	 *
	 * @param Orbis_Plugin $plugin
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;

		// Actions
		add_action( 'admin_init', array( $this, 'admin_init' ) );
	}

	//////////////////////////////////////////////////

	/**
	 * Admin initalize
	 */
	public function admin_init() {
		add_settings_section(
			'orbis_keychains',
			__( 'Keychains', 'orbis_keychains' ),
			'__return_false',
			'orbis'
		);

		add_settings_field(
			'orbis_keychains_word_count',
			__( 'Word Count', 'orbis_keychains' ),
			array( $this, 'input_text' ),
			'orbis',
			'orbis_keychains',
			array( 'label_for' => 'orbis_keychains_word_count' )
		);

		register_setting( 'orbis', 'orbis_keychains_word_count' );
	}

	//////////////////////////////////////////////////
	/**
	 * Input text
	 *
	 * @param array $args
	 */
	public function input_text( $args = array() ) {
		printf(
			'<input name="%s" id="%s" type="text" value="%s" class="%s" />',
			esc_attr( $args['label_for'] ),
			esc_attr( $args['label_for'] ),
			esc_attr( get_option( $args['label_for'] ) ),
			'regular-text code'
		);

		if ( isset( $args['description'] ) ) {
			printf(
				'<p class="description">%s</p>',
				$args['description']
			);
		}
	}
}
