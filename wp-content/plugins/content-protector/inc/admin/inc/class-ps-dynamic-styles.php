<?php

namespace passster;

class PS_Dynamic_Styles {
	/**
	 * Contains instance or null
	 *
	 * @var object|null
	 */
	private static $instance = null;

	/**
	 * Returns instance of PS_Dynamic_Styles.
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
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'wp_head', array( $this, 'dynamic_styles' ) );
	}

	/**
	 * Add dynamic styles
	 *
	 * @return void
	 */
	public function dynamic_styles() {
		$options = get_option( 'passster' );

		if ( isset( $options['button_font_size'] ) ) {
			$button_font_size = $options['button_font_size'];
		} else {
			$button_font_size = 12;
		}

		if ( isset( $options['button_font_weight'] ) ) {
			$button_font_weight = $options['button_font_weight'];
		} else {
			$button_font_weight = 400;
		}

		// Set default padding/margin if missing.
		$form_spacing = [
			'margin'  => [
				'top'    => $options['form_margin']['top'] ?? 0,
				'right'  => $options['form_margin']['right'] ?? 0,
				'bottom' =>  $options['form_margin']['bottom'] ?? 0,
				'left'   =>  $options['form_margin']['left'] ?? 0,
			],
			'padding' => [
				'top'    => $options['form_padding']['top'] ?? '10px',
				'right'  => $options['form_padding']['right'] ?? '30px',
				'bottom' => $options['form_padding']['bottom'] ?? '30px',
				'left'   => $options['form_padding']['left'] ?? '30px',
			],
		];

		$button_spacing = [
			'margin'  => [
				'top'    => $options['button_margin']['top'] ?? 0,
				'right'  => $options['button_margin']['right'] ?? 0,
				'bottom' => $options['button_margin']['bottom'] ?? 0,
				'left'   => $options['button_margin']['left'] ?? 0,
			],
			'padding' => [
				'top'    => $options['button_padding']['top'] ?? '10px',
				'right'  => $options['button_padding']['right'] ?? '10px',
				'bottom' => $options['button_padding']['bottom'] ?? '10px',
				'left'   => $options['button_padding']['left'] ?? '10px',
			],
		];

		?>
        <style>
            .passster-form {
                max-width: <?php echo esc_html($options['form_max_width']); ?> !important;
            <?php if( isset($options['center_form']) && $options['center_form']) : ?> margin: 0 auto !important;
            <?php endif; ?>
            }

            .passster-form > form {
                background: <?php echo esc_html($options['form_background_color']); ?>;
                padding: <?php echo esc_html($form_spacing['padding']['top']); ?> <?php echo esc_html($form_spacing['padding']['right']); ?> <?php echo esc_html($form_spacing['padding']['bottom']); ?> <?php echo esc_html($form_spacing['padding']['left']); ?>;
                margin: <?php echo esc_html($form_spacing['margin']['top']); ?> <?php echo esc_html($form_spacing['margin']['right']); ?> <?php echo esc_html($form_spacing['margin']['bottom']); ?> <?php echo esc_html($form_spacing['margin']['left']); ?>;
                border-radius: <?php echo esc_html($options['form_border_radius']); ?>px;
            }

            .passster-form h4 {
                font-size: <?php echo esc_html($options['headline_font_size']); ?>px;
                font-weight: <?php echo esc_html($options['headline_font_weight']); ?>;
                color: <?php echo esc_html($options['headline_font_color']); ?>;
            }

            .passster-form p {
                font-size: <?php echo esc_html($options['instruction_font_size']); ?>px;
                font-weight: <?php echo esc_html($options['instruction_font_weight']); ?>;
                color: <?php echo esc_html($options['instruction_font_color']); ?>;
            }

            .passster-submit, .passster-submit-recaptcha {
                background: <?php echo esc_html($options['button_background_color']); ?>;
                padding: <?php echo esc_html($button_spacing['padding']['top']); ?> <?php echo esc_html($button_spacing['padding']['right']); ?> <?php echo esc_html($button_spacing['padding']['bottom']); ?> <?php echo esc_html($button_spacing['padding']['left']); ?>;
                margin: <?php echo esc_html($button_spacing['margin']['top']); ?> <?php echo esc_html($button_spacing['margin']['right']); ?> <?php echo esc_html($button_spacing['margin']['bottom']); ?> <?php echo esc_html($button_spacing['margin']['left']); ?>;
                border-radius: <?php echo esc_html($options['button_border_radius']); ?>px;
                font-size: <?php echo esc_html($button_font_size); ?>px;
                font-weight: <?php echo esc_html($button_font_weight); ?>;
                color: <?php echo esc_html($options['button_font_color']); ?>;
            }

            .passster-submit:hover, .passster-submit-recaptcha:hover {
                background: <?php echo esc_html($options['button_background_color_hover']); ?>;
                color: <?php echo esc_html($options['button_font_color_hover']); ?>;
            }
        </style>
		<?php
	}
}
