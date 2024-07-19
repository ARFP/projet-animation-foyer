<?php

namespace passster;

class PS_Migrator {

	/**
	 * Handle the migration from v3 to v4 in Passster.
	 *
	 * @return void
	 */
	public static function migrate() {
		// Reset new settings first.
		$options = array();

		// Start migration.
		$options = self::migrate_customizer_settings( $options );
		$options = self::migrate_options( $options );

		update_option( 'passster', $options );

		// Now we can delete the old options.
		self::delete_options();
	}

	/**
	 * Migrates customizer settings to option.
	 *
	 * @param $options
	 *
	 * @return mixed
	 */
	public static function migrate_customizer_settings( $options ) {
		$customizer_data = array(
			'headline'                      => get_theme_mod( 'passster_form_instructions_headline' ),
			'instruction'                   => get_theme_mod( 'passster_form_instructions_text' ),
			'placeholder'                   => get_theme_mod( 'passster_form_instructions_placeholder' ),
			'error'                         => get_theme_mod( 'passster_form_error_text' ),
			'button_label'                  => get_theme_mod( 'passster_form_button_label' ),
			'show_password'                 => get_theme_mod( 'passster_form_instructions_password_typing' ),
			'form_background_color'         => get_theme_mod( 'passster_form_general_background_color' ),
			'headline_font_color'           => get_theme_mod( 'passster_form_instructions_headline_color' ),
			'headline_font_size'            => get_theme_mod( 'passster_form_instructions_headline_font_size' ),
			'headline_font_weight'          => get_theme_mod( 'passster_form_instructions_headline_font_weight' ),
			'instruction_font_color'        => get_theme_mod( 'passster_form_instructions_text_color' ),
			'instruction_font_size'         => get_theme_mod( 'passster_form_instructions_text_font_size' ),
			'instruction_font_weight'       => get_theme_mod( 'passster_form_instructions_text_font_weight' ),
			'button_background_color'       => get_theme_mod( 'passster_form_button_background_color' ),
			'button_font_color'             => get_theme_mod( 'passster_form_button_text_color' ),
			'button_background_color_hover' => get_theme_mod( 'passster_form_button_background_hover_color' ),
			'button_font_color_hover'       => get_theme_mod( 'passster_form_button_text_hover_color' ),
			'form_max_width'                => '700px',
			'form_border_radius'            => 0,
			'form_padding'                  => array(
				'top'    => '20px',
				'right'  => '20px',
				'bottom' => '20px',
				'left'   => '20px'
			),
			'form_margin'                   => array(
				'top'    => '0px',
				'right'  => '0px',
				'bottom' => '0px',
				'left'   => '0px'
			),
			'button_padding'                => array(
				'top'    => '10px',
				'right'  => '10px',
				'bottom' => '10px',
				'left'   => '10px'
			),
			'button_margin'                 => array(
				'top'    => '0px',
				'right'  => '0px',
				'bottom' => '0px',
				'left'   => '0px'
			),
			'button_border_radius'          => 0,
		);

		$default_data = array(
			'headline'                      => __( 'Protected Area', 'content-protector' ),
			'instruction'                   => __( 'This content is password-protected. Please verify with a password to unlock the content.', 'content-protector' ),
			'placeholder'                   => __( 'Enter your password..', 'content-protector' ),
			'error'                         => __( 'Sorry, there was an error.', 'content-protector' ),
			'button_label'                  => __( 'Unlock', 'content-protector' ),
			'show_password'                 => false,
			'form_background_color'         => '#FAFAFA',
			'headline_font_color'           => '#6804cc',
			'headline_font_size'            => 24,
			'headline_font_weight'          => 500,
			'instruction_font_color'        => '#000',
			'instruction_font_size'         => 16,
			'instruction_font_weight'       => 300,
			'button_background_color'       => '#6804cc',
			'button_font_color'             => '#fff',
			'button_background_color_hover' => '#000',
			'button_font_color_hover'       => '#fff',
			'form_border_radius'            => 0,
			'button_border_radius'          => 0,
		);

		foreach ( $customizer_data as $option => $value ) {
			if ( ! empty( $value ) ) {
				$options[ $option ] = $value;
			} else {
				$options[ $option ] = $default_data[ $option ];
			}
		}

		return $options;
	}

	/**
	 * Migrate old options to new option.
	 *
	 * @param $options
	 *
	 * @return mixed
	 */
	public static function migrate_options( $options ) {
		// Migrate general settings.
		$general_settings = get_option( 'passster_general_settings' );

		if ( ! is_array( $general_settings ) ) {
			return $options;
		}

		if ( 'on' !== $general_settings['toggle_cookie'] ) {
			$options['disable_cookie'] = false;
		}

		if ( 'on' === $general_settings['toggle_ajax'] ) {
			$options['unlock_mode'] = true;
		}

		if ( 'on' === $general_settings['passster_activate_concurrent'] ) {
			$options['use_concurrent'] = true;
		}

		if ( 'on' === $general_settings['passster_activate_concurrent'] ) {
			$options['use_concurrent'] = true;
		}

		if ( ! empty( $general_settings['passster_cookie_duration'] ) ) {
			$options['cookie_duration'] = $general_settings['passster_cookie_duration'];
		} else {
			$options['cookie_duration'] = "1";
		}

		$options['third_party_shortcodes'] = $general_settings['third_party_shortcodes'];
		$options['number_of_concurrents']  = $general_settings['passster_number_of_concurrent_logins'];

		update_option( 'passster', $options );

		// Migrate advanced settings.
		$advanced_settings = get_option( 'passster_advanced_settings' );

		if ( ! is_array( $advanced_settings ) ) {
			return $options;
		}

		$options['recaptcha_version']  = $advanced_settings['passster_recaptcha_type'];
		$options['recaptcha_site_key'] = $advanced_settings['passster_recaptcha_site_key'];
		$options['recaptcha_secret']   = $advanced_settings['passster_recaptcha_secret'];
		$options['recaptcha_language'] = $advanced_settings['passster_recaptcha_language'];
		$options['bitly_token']        = $advanced_settings['passster_bitly_access_key'];

		// Migrate Global Protection settings.
		$options['global_protection_id'] = get_option( 'passster_global_id' );

		// Also add new options to avoid missing keys on migration.
		$options['password_length']      = "6";
		$options['include_uppercase']    = false;
		$options['include_numbers']      = false;
		$options['cookie_duration_unit'] = 'days';

		return $options;
	}

	/**
	 * Delete old options.
	 *
	 * @return void
	 */
	public static function delete_options() {
		// Delete theme mods.
		remove_theme_mod( 'passster_form_instructions_headline' );
		remove_theme_mod( 'passster_form_instructions_text' );
		remove_theme_mod( 'passster_form_instructions_placeholder' );
		remove_theme_mod( 'passster_form_error_text' );
		remove_theme_mod( 'passster_form_button_label' );
		remove_theme_mod( 'passster_form_instructions_password_typing' );
		remove_theme_mod( 'passster_form_general_background_color' );
		remove_theme_mod( 'passster_form_instructions_headline_font_size' );
		remove_theme_mod( 'passster_form_instructions_headline_font_weight' );
		remove_theme_mod( 'passster_form_instructions_text_color' );
		remove_theme_mod( 'passster_form_instructions_text_font_size' );
		remove_theme_mod( 'passster_form_instructions_text_font_weight' );
		remove_theme_mod( 'passster_form_button_background_color' );
		remove_theme_mod( 'passster_form_button_text_color' );
		remove_theme_mod( 'passster_form_button_background_hover_color' );
		remove_theme_mod( 'passster_form_button_text_hover_color' );

		// Delete old options.
		delete_option( 'passster_general_settings' );
		delete_option( 'passster_advanced_settings' );
		delete_option( 'passster_global_id' );
	}
}
