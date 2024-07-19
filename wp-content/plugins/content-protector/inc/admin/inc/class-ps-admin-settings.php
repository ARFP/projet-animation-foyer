<?php

namespace passster;

class PS_Admin_Settings
{
    /**
     * Contains instance or null
     *
     * @var object|null
     */
    private static  $instance = null ;
    /**
     * Returns instance of PS_Admin_Settings.
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
     * Setting up admin fields
     *
     * @return void
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_menu' ) );
        add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
    }
    
    public function add_menu()
    {
        add_menu_page(
            __( 'Passster', 'content-protector' ),
            'Passster',
            apply_filters( 'passster_user_capability', 'manage_options' ),
            'passster',
            array( $this, 'render_settings' ),
            'dashicons-lock',
            85
        );
        $settings_suffix = add_submenu_page(
            'passster',
            __( 'Options', 'content-protector' ),
            __( 'Settings', 'content-protector' ),
            apply_filters( 'passster_user_capability', 'manage_options' ),
            'passster-settings',
            array( $this, 'render_settings' )
        );
        add_action( "admin_print_scripts-{$settings_suffix}", array( $this, 'add_settings_scripts' ) );
    }
    
    public function add_settings_scripts()
    {
        $screen = get_current_screen();
        wp_enqueue_script(
            'passster-settings',
            PASSSTER_URL . '/inc/admin/build/index.js',
            array(
            'wp-api',
            'wp-components',
            'wp-element',
            'wp-api-fetch',
            'wp-data',
            'wp-i18n'
        ),
            PASSSTER_VERSION,
            true
        );
        $options = get_option( 'passster' );
        $args = array(
            'screen'  => 'passster-settings',
            'version' => PASSSTER_VERSION,
            'logo'    => PASSSTER_URL . '/assets/admin/passster-logo.svg',
            'is_pro'  => \passster_fs()->is_plan_or_trial__premium_only( 'pro' ),
        );
        if ( isset( $options['global_protection_id'] ) ) {
            $args['global_edit_url'] = admin_url( 'post.php?post=' . esc_html( $options['global_protection_id'] ) . '&action=edit', 'https' );
        }
        wp_localize_script( 'passster-settings', 'options', $args );
        // Make the blocks translatable.
        if ( function_exists( 'wp_set_script_translations' ) ) {
            wp_set_script_translations( 'passster-settings', 'content-protector', PASSSTER_PATH . '/languages' );
        }
        wp_enqueue_style( 'passster-settings-style', PASSSTER_URL . '/inc/admin/build/index.css', array( 'wp-components' ) );
    }
    
    public function render_settings()
    {
        ?>
        <div id="passster-settings"></div>
		<?php 
    }
    
    public function rest_api_init()
    {
        register_rest_route( 'passster/v1', '/settings', array(
            'methods'             => 'GET',
            'callback'            => [ $this, 'get_settings' ],
            'permission_callback' => function () {
            return current_user_can( apply_filters( 'passster_user_capability', 'manage_options' ) );
        },
        ) );
        register_rest_route( 'passster/v1', '/system-status', array(
            'methods'             => 'GET',
            'callback'            => [ $this, 'get_system_status' ],
            'permission_callback' => function () {
            return current_user_can( apply_filters( 'passster_user_capability', 'manage_options' ) );
        },
        ) );
        register_rest_route( 'passster/v1', '/settings', array(
            'methods'             => 'POST',
            'callback'            => [ $this, 'save_settings' ],
            'permission_callback' => function () {
            return current_user_can( apply_filters( 'passster_user_capability', 'manage_options' ) );
        },
        ) );
        register_rest_route( 'passster/v1', '/migrate', array(
            'methods'             => 'POST',
            'callback'            => [ $this, 'migrate_settings' ],
            'permission_callback' => function () {
            return current_user_can( apply_filters( 'passster_user_capability', 'manage_options' ) );
        },
        ) );
        register_rest_route( 'passster/v1', '/pages', array(
            'methods'             => 'GET',
            'callback'            => [ $this, 'get_pages' ],
            'permission_callback' => function () {
            return current_user_can( apply_filters( 'passster_user_capability', 'manage_options' ) );
        },
        ) );
        register_rest_route( 'passster/v1', '/excludable-pages', array(
            'methods'             => 'GET',
            'callback'            => [ $this, 'get_excludable_pages' ],
            'permission_callback' => function () {
            return current_user_can( apply_filters( 'passster_user_capability', 'manage_options' ) );
        },
        ) );
        register_rest_route( 'passster/v1', '/edit-url', array(
            'methods'             => 'GET',
            'callback'            => [ $this, 'get_edit_url' ],
            'permission_callback' => function () {
            return current_user_can( apply_filters( 'passster_user_capability', 'manage_options' ) );
        },
        ) );
        register_rest_route( 'passster/v1', '/post-title', array(
            'methods'             => 'GET',
            'callback'            => [ $this, 'get_post_title' ],
            'permission_callback' => function () {
            return current_user_can( apply_filters( 'passster_user_capability', 'manage_options' ) );
        },
        ) );
        register_rest_route( 'passster/v1', '/child-pages', array(
            'methods'             => 'GET',
            'callback'            => [ $this, 'get_child_pages' ],
            'permission_callback' => function () {
            return current_user_can( apply_filters( 'passster_user_capability', 'manage_options' ) );
        },
        ) );
    }
    
    public function get_settings()
    {
        return get_option( 'passster' );
    }
    
    public function get_system_status()
    {
        return array(
            'PHP'       => array(
            'Version' => phpversion(),
        ),
            'WordPress' => array(
            'Permalinks' => strlen( get_option( 'permalink_structure' ) ) !== 0,
            'SSL'        => is_ssl(),
        ),
        );
    }
    
    public function save_settings( $request )
    {
        
        if ( $request->get_params() ) {
            $options = sanitize_option( 'passster', $request->get_params() );
            foreach ( $options as $key => $value ) {
                if ( $key !== 'exclude_pages' && !is_array( $value ) ) {
                    $options[$key] = sanitize_text_field( $value );
                }
            }
            update_option( 'passster', $options );
        }
        
        return wp_json_encode( [
            "status"  => 200,
            "message" => "Ok",
        ] );
    }
    
    public function migrate_settings()
    {
        PS_Migrator::migrate();
        return wp_json_encode( [
            "status"  => 200,
            "message" => "Ok",
        ] );
    }
    
    public function get_pages()
    {
        $args = array(
            'post_type'   => 'page',
            'post_status' => 'publish',
            'numberposts' => -1,
        );
        $pages = get_posts( $args );
        // Build selectable pages array.
        $selectable_pages = array();
        foreach ( $pages as $page ) {
            $selectable_pages[] = array(
                'label' => $page->post_title,
                'value' => $page->ID,
            );
        }
        return $selectable_pages;
    }
    
    public function get_excludable_pages()
    {
        $args = array(
            'post_type'   => 'page',
            'post_status' => 'publish',
            'numberposts' => -1,
        );
        $pages = get_posts( $args );
        // Build selectable pages array.
        $selectable_pages = array();
        foreach ( $pages as $page ) {
            $object = new \StdClass();
            $object->value = $page->ID;
            $object->label = $page->post_title;
            $selectable_pages[] = $object;
        }
        return $selectable_pages;
    }
    
    public function get_edit_url( $request )
    {
        
        if ( $request->get_params() ) {
            $options = $request->get_params();
            return admin_url( 'post.php?post=' . esc_html( $options['post_id'] ) . '&action=edit', 'https' );
        }
        
        return wp_json_encode( [
            "status"  => 400,
            "message" => "Not found",
        ] );
    }
    
    public function get_post_title( $request )
    {
        
        if ( $request->get_params() ) {
            $options = $request->get_params();
            $post_id = esc_html( $options['post_id'] );
            if ( get_post_status( $post_id ) && 'publish' === get_post_status( $post_id ) ) {
                return get_the_title( $post_id );
            }
        }
        
        return false;
    }
    
    public function get_child_pages( $request )
    {
        
        if ( $request->get_params() ) {
            $options = $request->get_params();
            $parent_id = esc_html( $options['post_id'] );
            $args = array(
                'post_type'   => 'page',
                'post_status' => 'publish',
                'numberposts' => -1,
            );
            $pages = get_posts( $args );
            $childs = get_page_children( $parent_id, $pages );
            $child_ids = array();
            foreach ( $childs as $child ) {
                $child_ids[] = $child->ID;
            }
            return $child_ids;
        }
        
        return wp_json_encode( [
            "status"  => 400,
            "message" => "Not found",
        ] );
    }

}