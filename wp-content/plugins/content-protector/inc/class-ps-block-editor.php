<?php

namespace passster;

class PS_Block_Editor
{
    /**
     * Contains instance or null
     *
     * @var object|null
     */
    private static  $instance = null ;
    /**
     * Returns instance of PS_Block_Editor.
     *
     * @return object
     */
    public static function get_instance()
    {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor for PS_Block_Editor
     */
    public function __construct()
    {
        add_action( 'wp_enqueue_scripts', array( $this, 'register_block_scripts' ) );
        add_action( 'enqueue_block_editor_assets', array( $this, 'add_block_editor_assets' ) );
        add_action( 'init', array( $this, 'register_area_block' ) );
    }
    
    /**
     * Enqueue scripts for blocks.
     *
     * @return void
     */
    public function register_block_scripts()
    {
        $area_asset_file = (include PASSSTER_PATH . '/build/area/index.asset.php');
        wp_register_script(
            'area-script',
            PASSSTER_URL . '/build/area/index.js',
            $area_asset_file['dependencies'],
            $area_asset_file['version']
        );
    }
    
    /**
     * Add block editor scripts and styles.
     *
     * @return void
     */
    public function add_block_editor_assets()
    {
        $area_asset_file = (include PASSSTER_PATH . '/build/area/index.asset.php');
        wp_enqueue_script(
            'area-script',
            PASSSTER_URL . '/build/area/index.js',
            $area_asset_file['dependencies'],
            $area_asset_file['version']
        );
        wp_localize_script( 'area-script', 'area_data', array(
            'is_pro'          => \passster_fs()->is_plan_or_trial__premium_only( 'pro' ),
            'create_area_url' => admin_url( 'post-new.php?post_type=protected_areas' ),
            'options'         => get_option( 'passster' ),
        ) );
        wp_enqueue_style( 'area-style', PASSSTER_URL . '/build/area/index.css' );
        // Make the blocks translatable.
        if ( function_exists( 'wp_set_script_translations' ) ) {
            wp_set_script_translations( 'area-script', 'content-protector', PASSSTER_PATH . '/languages' );
        }
    }
    
    /**
     * Register area block in WordPress.
     *
     * @return void
     */
    public function register_area_block()
    {
        $options = get_option( 'passster' );
        $settings = array(
            'render_callback' => array( $this, 'render_area_block' ),
            'attributes'      => array(
            'area_id'     => array(
            'type'    => 'string',
            'default' => 0,
        ),
            'headline'    => array(
            'type'    => 'string',
            'default' => esc_html( $options['headline'] ),
        ),
            'instruction' => array(
            'type'    => 'string',
            'default' => wp_kses_post( $options['instruction'] ),
        ),
            'buttonLabel' => array(
            'type'    => 'string',
            'default' => esc_html( $options['button_label'] ),
        ),
            'placeholder' => array(
            'type'    => 'string',
            'default' => esc_html( $options['placeholder'] ),
        ),
        ),
        );
        register_block_type( 'content-protector/area', array_merge( array(
            'editor_script' => 'area-script',
            'editor_style'  => 'area-style',
        ), $settings ) );
    }
    
    /**
     * Returns the shortcode for an area based on block attributes.
     *
     * @param array $attributes the list attributes from the block.
     *
     * @return string
     */
    public function render_area_block( array $attributes )
    {
        $area_id = esc_attr( $attributes['area_id'] );
        $area = get_post( $area_id );
        if ( !$area ) {
            return '';
        }
        // Build dynamic shortcode.
        $content = $area->post_content;
        $shortcode = '';
        // texts.
        $headline = esc_attr( $attributes['headline'] );
        $instruction = wp_kses_post( $attributes['instruction'] );
        $button = esc_attr( $attributes['buttonLabel'] );
        $placeholder = esc_attr( $attributes['placeholder'] );
        $id = get_post_meta( $area_id, 'passster_id', true );
        // Redirection.
        $redirection = get_post_meta( $area_id, 'passster_redirect_url', true );
        $atts = array();
        $password = get_post_meta( $area_id, 'passster_password', true );
        $atts['password'] = $password;
        $shortcode = '[passster password="' . $password . '" area="' . $area_id . '" ';
        // Building the actual shortcode.
        if ( !empty($redirection) ) {
            $shortcode .= 'redirect="' . $redirection . '" ';
        }
        if ( !empty($headline) ) {
            $shortcode .= 'headline="' . $headline . '" ';
        }
        if ( !empty($instruction) ) {
            $shortcode .= 'instruction="' . $instruction . '" ';
        }
        if ( !empty($placeholder) ) {
            $shortcode .= 'placeholder="' . $placeholder . '" ';
        }
        if ( !empty($button) ) {
            $shortcode .= 'button="' . $button . '" ';
        }
        if ( !empty($id) ) {
            $shortcode .= 'id="' . $id . '" ';
        }
        $shortcode = rtrim( $shortcode );
        $shortcode .= ']';
        // check if valid before restrict anything.
        $valid = PS_Conditional::is_valid( $atts );
        if ( $valid ) {
            return $content;
        }
        return do_shortcode( $shortcode );
    }

}