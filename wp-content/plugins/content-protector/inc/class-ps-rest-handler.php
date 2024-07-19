<?php

namespace passster;

class PS_Rest_Handler {

	/**
	 * Contains instance or null
	 *
	 * @var object|null
	 */
	private static $instance = null;

	/**
	 * Returns instance of PS_Rest_Handler.
	 *
	 * @return object
	 */
	public static function get_instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor for PS_Rest_Handler
	 */
	public function __construct() {
		add_filter( 'rest_authentication_errors', array( $this, 'restrict_rest_access' ) );
	}

	public function restrict_rest_access( $result ) {

		// If a previous authentication check was applied,
		// pass that result along without modification.
		if ( true === $result || is_wp_error( $result ) ) {
			return $result;
		}

		// Check if request is coming from a frontend page builder.
		if ( current_user_can( 'administrator' ) && ( is_plugin_active( 'elementor/elementor.php' ) || is_plugin_active( 'livecanvas/livecanvas-plugin-index.php' ) || is_plugin_active( 'divi-builder/divi-builder.php' ) || is_plugin_active( 'oxygen/functions.php' ) || is_plugin_active( 'pagelayer/pagelayer.php' ) ) ) {
			return $result;
		}

		// Global protection activated?
		$settings           = get_option( 'passster' );
		$protection_enabled = $settings['activate_global_protection'] ?? false;
		$page_id            = $settings['global_protection_id'];

		// Check if access is allowed.
		$atts  = array( 'password' => get_post_meta( $page_id, 'passster_password', true ) );
		$valid = PS_Conditional::is_valid( $atts );

		if ( $protection_enabled && ! $valid && ! is_user_logged_in() ) {
			return new \WP_Error(
				'rest_not_logged_in',
				__( 'You are not allowed to access this content. Please authenticate with a password first.' ),
				array( 'status' => 401 )
			);
		}

		return $result;
	}
}

