<?php

namespace passster;

class PS_Meta
{
    /**
     * Contains instance or null
     *
     * @var object|null
     */
    private static  $instance = null ;
    /**
     * Returns instance of PS_Meta.
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
     * Setting up meta fields
     *
     * @return void
     */
    public function __construct()
    {
        add_action( 'init', array( $this, 'register_meta_fields' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'add_meta_scripts' ) );
        add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
        add_action( 'add_meta_boxes', array( $this, 'add_metaboxes' ) );
    }
    
    /**
     * Register meta fields in WordPress.
     *
     * @return void
     */
    public function register_meta_fields()
    {
        register_meta( 'post', 'passster_activate_protection', array(
            'single'       => true,
            'type'         => 'boolean',
            'show_in_rest' => true,
            'default'      => false,
        ) );
        register_meta( 'post', 'passster_protect_child_pages', array(
            'single'       => true,
            'show_in_rest' => true,
        ) );
        register_meta( 'post', 'passster_protection_type', array(
            'single'       => true,
            'show_in_rest' => true,
            'default'      => 'password',
        ) );
        register_meta( 'post', 'passster_password', array(
            'single'       => true,
            'show_in_rest' => true,
        ) );
        register_meta( 'post', 'passster_activate_overwrite_defaults', array(
            'single'       => true,
            'show_in_rest' => true,
        ) );
        register_meta( 'post', 'passster_headline', array(
            'single'       => true,
            'show_in_rest' => true,
        ) );
        register_meta( 'post', 'passster_instruction', array(
            'single'       => true,
            'show_in_rest' => true,
        ) );
        register_meta( 'post', 'passster_placeholder', array(
            'single'       => true,
            'show_in_rest' => true,
        ) );
        register_meta( 'post', 'passster_button', array(
            'single'       => true,
            'show_in_rest' => true,
        ) );
        register_meta( 'post', 'passster_id', array(
            'single'       => true,
            'show_in_rest' => true,
        ) );
        register_meta( 'post', 'passster_activate_misc_settings', array(
            'single'       => true,
            'show_in_rest' => true,
        ) );
        register_meta( 'post', 'passster_redirect_url', array(
            'single'       => true,
            'show_in_rest' => true,
            'default'      => '',
        ) );
        register_meta( 'post', 'passster_hide', array(
            'single'       => true,
            'show_in_rest' => true,
            'default'      => 'no',
        ) );
        register_meta( 'post', 'passster_area_shortcode', array(
            'single'       => true,
            'show_in_rest' => true,
        ) );
    }
    
    /**
     * Add scripts to post edit screen.
     *
     * @return void
     */
    public function add_meta_scripts()
    {
        $screen = get_current_screen();
        if ( $screen->base !== 'post' ) {
            return;
        }
        wp_enqueue_script(
            'passster-settings',
            PASSSTER_URL . '/inc/admin/build/index.js',
            array(
            'wp-api',
            'wp-components',
            'wp-element',
            'wp-api-fetch',
            'wp-data',
            'wp-editor',
            'wp-i18n'
        ),
            PASSSTER_VERSION,
            true
        );
        $post_id = get_the_id();
        $options = get_option( 'passster' );
        $meta = array(
            'passster_protection_type'             => get_post_meta( $post_id, 'passster_protection_type', true ),
            'passster_password'                    => get_post_meta( $post_id, 'passster_password', true ),
            'passster_headline'                    => get_post_meta( $post_id, 'passster_headline', true ),
            'passster_instruction'                 => get_post_meta( $post_id, 'passster_instruction', true ),
            'passster_placeholder'                 => get_post_meta( $post_id, 'passster_placeholder', true ),
            'passster_button'                      => get_post_meta( $post_id, 'passster_button', true ),
            'passster_id'                          => get_post_meta( $post_id, 'passster_id', true ),
            'passster_hide'                        => get_post_meta( $post_id, 'passster_hide', true ),
            'passster_redirect_url'                => get_post_meta( $post_id, 'passster_redirect_url', true ),
            'passster_activate_protection'         => get_post_meta( $post_id, 'passster_activate_protection', true ),
            'passster_protect_child_pages'         => get_post_meta( $post_id, 'passster_protect_child_pages', true ),
            'passster_activate_overwrite_defaults' => get_post_meta( $post_id, 'passster_activate_overwrite_defaults', true ),
            'passster_activate_misc_settings'      => get_post_meta( $post_id, 'passster_activate_misc_settings', true ),
        );
        $args = array(
            'screen'    => 'meta',
            'is_pro'    => \passster_fs()->is_plan_or_trial__premium_only( 'pro' ),
            'meta'      => $meta,
            'post_type' => $this->get_current_post_type(),
            'post_id'   => $post_id,
        );
        // Optionally adding some settings.
        
        if ( isset( $options['password_length'] ) ) {
            $args['password_length'] = esc_html( $options['password_length'] );
        } else {
            $args['password_length'] = 6;
        }
        
        
        if ( isset( $options['include_uppercase'] ) ) {
            $args['include_uppercase'] = esc_html( $options['include_uppercase'] );
        } else {
            $args['include_uppercase'] = false;
        }
        
        
        if ( isset( $options['include_numbers'] ) ) {
            $args['include_numbers'] = esc_html( $options['include_numbers'] );
        } else {
            $args['include_numbers'] = false;
        }
        
        if ( isset( $options['global_protection_id'] ) ) {
            $args['global_protection_id'] = esc_html( $options['global_protection_id'] );
        }
        wp_localize_script( 'passster-settings', 'options', $args );
        // Make the blocks translatable.
        if ( function_exists( 'wp_set_script_translations' ) ) {
            wp_set_script_translations( 'passster-settings', 'content-protector', PASSSTER_PATH . '/languages' );
        }
        wp_enqueue_style( 'passster-settings-style', PASSSTER_URL . '/inc/admin/build/index.css', array( 'wp-components' ) );
    }
    
    /**
     * Adds the meta box container.
     *
     * @return void
     */
    public function add_metaboxes()
    {
        $post_types = get_post_types( array(
            'public'              => true,
            'exclude_from_search' => false,
        ), 'names' );
        $post_types[] = 'protected_areas';
        add_meta_box(
            'passster',
            __( 'Passster', 'content-protector' ),
            array( $this, 'render_settings' ),
            apply_filters( 'passster_post_types', $post_types ),
            'side',
            'default'
        );
    }
    
    /**
     * Render meta fields.
     *
     * @return void
     */
    public function render_settings()
    {
        ?>
        <div id="passster-metabox"></div>
		<?php 
    }
    
    /**
     * Register custom Rest API routes.
     *
     * @return void
     */
    public function rest_api_init()
    {
        register_rest_route( 'passster/v1', '/meta', array(
            'methods'             => 'POST',
            'callback'            => [ $this, 'save_meta' ],
            'permission_callback' => function () {
            return current_user_can( apply_filters( 'passster_user_capability', 'manage_options' ) );
        },
        ) );
        register_rest_route( 'passster/v1', '/meta', array(
            'methods'             => 'GET',
            'callback'            => [ $this, 'get_meta' ],
            'permission_callback' => function () {
            return current_user_can( apply_filters( 'passster_user_capability', 'manage_options' ) );
        },
        ) );
        register_rest_route( 'passster/v1', '/areas', array(
            'methods'             => 'GET',
            'callback'            => [ $this, 'get_areas' ],
            'permission_callback' => function () {
            return current_user_can( apply_filters( 'passster_user_capability', 'manage_options' ) );
        },
        ) );
    }
    
    /**
     * Save meta via Rest API.
     *
     * @param object $request given request.
     *
     * @return false|string|void
     */
    public function save_meta( object $request )
    {
        
        if ( $request->get_params() ) {
            $params = $request->get_params();
            $post_id = esc_html( $params['post_id'] );
            $meta_key = esc_html( $params['meta_key'] );
            // Check which action to perform.
            
            if ( isset( $params['meta_value'] ) ) {
                $meta_value = sanitize_meta( $meta_key, $params['meta_value'], 'post' );
                update_post_meta( $post_id, $meta_key, $meta_value );
            } else {
                if ( isset( $params['delete'] ) ) {
                    delete_post_meta( $post_id, $meta_key );
                }
            }
            
            return wp_json_encode( [
                "status"  => 200,
                "message" => "Ok",
            ] );
        }
    
    }
    
    /**
     * Get meta via Rest API.
     *
     * @param object $request given request.
     *
     * @return false|string|void
     */
    public function get_meta( object $request )
    {
        
        if ( $request->get_params() ) {
            $params = $request->get_params();
            $post_id = esc_html( $params['post_id'] );
            $meta_key = esc_html( $params['meta_key'] );
            $meta = get_post_meta( $post_id, $meta_key, true );
            
            if ( !empty($meta) ) {
                return wp_json_encode( [
                    "status"  => 200,
                    "message" => "Ok",
                    "data"    => $meta,
                ] );
            } else {
                return wp_json_encode( [
                    "status"  => 400,
                    "message" => "Empty value",
                    "data"    => '',
                ] );
            }
        
        }
    
    }
    
    /**
     * Get areas via REST API.
     *
     * @return array
     */
    public function get_areas() : array
    {
        $args = array(
            'post_type'   => 'protected_areas',
            'post_status' => 'publish',
            'numberposts' => -1,
        );
        $areas = get_posts( $args );
        $fetched_areas = array();
        foreach ( $areas as $area ) {
            $new_area = new \stdClass();
            $new_area->title = $area->post_title;
            $new_area->id = $area->ID;
            $fetched_areas[] = $new_area;
        }
        return $fetched_areas;
    }
    
    /**
     * Get the current post type.
     *
     * @return mixed|string|\WP_Post_Type|null
     */
    public function get_current_post_type()
    {
        global  $post, $typenow, $current_screen ;
        
        if ( $post && $post->post_type ) {
            return $post->post_type;
        } elseif ( $typenow ) {
            return $typenow;
        } elseif ( $current_screen && $current_screen->post_type ) {
            return $current_screen->post_type;
        }
        
        return null;
    }

}