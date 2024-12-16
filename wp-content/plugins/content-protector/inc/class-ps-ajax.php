<?php

namespace passster;

use  Exception ;
class PS_Ajax
{
    /**
     * Contains instance or null
     *
     * @var object|null
     */
    private static  $instance = null ;
    /**
     * Constructor for PS_Public
     */
    public function __construct()
    {
        add_action( 'wp_enqueue_scripts', array( $this, 'add_public_scripts' ) );
        add_action( 'wp_ajax_validate_input', array( $this, 'validate_input' ) );
        add_action( 'wp_ajax_nopriv_validate_input', array( $this, 'validate_input' ) );
        add_action( 'wp_ajax_hash_password', array( $this, 'hash_password' ) );
        add_action( 'wp_ajax_nopriv_hash_password', array( $this, 'hash_password' ) );
    }
    
    /**
     * Returns instance of PS_Public.
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
     * Validate ajax given input.
     *
     * @return void
     * @throws Exception
     */
    public function validate_input()
    {
        $options = get_option( 'passster' );
        // check nonce.
        
        if ( !wp_verify_nonce( $_POST['nonce'], 'ps-password-nonce' ) ) {
            $response = array(
                'error' => 'Security check failed.',
            );
            print wp_json_encode( $response );
            exit;
        }
        
        // Default response.
        $response = array(
            'error'   => $options['error'],
            'content' => '',
        );
        // Check if input exists.
        
        if ( !empty($_POST['input']) ) {
            $input = sanitize_text_field( $_POST['input'] );
        } else {
            $input = '';
        }
        
        // prepare validation.
        $remove_spaces = apply_filters( 'passster_remove_spaces_from_list', true );
        $type = sanitize_text_field( $_POST['type'] );
        $post_id = sanitize_text_field( $_POST['post_id'] );
        $protection = sanitize_text_field( $_POST['protection'] );
        // check protection.
        if ( empty($protection) ) {
            $protection = false;
        }
        // prepare content.
        $post = get_post( $post_id );
        $content = apply_filters( 'passster_compatibility_actions', $post->post_content, $post_id );
        // if it's an ACF Field.
        
        if ( !empty($_POST['acf']) ) {
            $acf = esc_html( $_POST['acf'] );
            $content = \get_field( $acf, $post_id );
        }
        
        // Check if redirection.
        $redirect = '';
        if ( !empty($_POST['redirect']) ) {
            $redirect = esc_url( $_POST['redirect'] );
        }
        switch ( $type ) {
            case 'password':
                // Get password.
                $password = get_post_meta( $post_id, 'passster_password', true );
                // Check protection type.
                switch ( $protection ) {
                    case 'full':
                        
                        if ( !empty($password) && $input === $password ) {
                            $validation = $this->validate_full_protection(
                                $input,
                                $post,
                                $content,
                                $redirect
                            );
                            if ( false !== $validation ) {
                                $response = $validation;
                            }
                        }
                        
                        break;
                    case 'area':
                        $area_id = esc_html( $_POST['area'] );
                        $password = get_post_meta( $area_id, 'passster_password', true );
                        
                        if ( !empty($area_id) && !empty($password) && $input === $password ) {
                            $area = get_post( $area_id );
                            $validation = $this->validate_area_protection( $input, $area, $redirect );
                            if ( false !== $validation ) {
                                $response = $validation;
                            }
                        }
                        
                        break;
                    default:
                        $content = apply_filters( 'passster_compatibility_actions', PS_Helper::get_shortcode_content( $content, $input ) );
                        $validation = $this->validate_shortcode_protection(
                            $input,
                            $content,
                            $redirect,
                            $options
                        );
                        if ( false !== $validation ) {
                            $response = $validation;
                        }
                }
                print wp_json_encode( $response );
                exit;
        }
        print wp_json_encode( $response );
        exit;
    }
    
    /**
     * @param $input string given input password.
     * @param $post object current post object.
     * @param $content string given content.
     * @param $redirect bool redirect or not.
     *
     * @return array|false
     */
    public function validate_full_protection(
        string $input,
        object $post,
        string $content,
        bool $redirect
    )
    {
        // Check that its published.
        if ( 'publish' !== $post->post_status ) {
            return false;
        }
        $response = array(
            'success' => true,
            'content' => $content,
        );
        do_action( 'passster_validation_success', $input );
        if ( $redirect ) {
            $response = array(
                'success'  => true,
                'redirect' => true,
            );
        }
        return $response;
    }
    
    /**
     * @param $input string given password.
     * @param $area object given area.
     * @param $redirect bool redirect or not.
     *
     * @return array|false
     */
    public function validate_area_protection( string $input, object $area, bool $redirect )
    {
        // Check that it is an area.
        if ( 'protected_areas' !== $area->post_type ) {
            return false;
        }
        // Check that its published.
        if ( 'publish' !== $area->post_status ) {
            return false;
        }
        $content = apply_filters( 'passster_compatibility_actions', $area->post_content, $area->ID );
        $response = array(
            'success' => true,
            'content' => $content,
        );
        do_action( 'passster_validation_success', $input );
        if ( $redirect ) {
            $response = array(
                'success'  => true,
                'redirect' => true,
            );
        }
        return $response;
    }
    
    /**
     * @param $input string given password.
     * @param $content string current content.
     * @param $redirect bool redirect or not.
     * @param $options array given options.
     *
     * @return array|bool[]|false
     */
    public function validate_shortcode_protection(
        string $input,
        string $content,
        bool $redirect,
        array $options
    )
    {
        
        if ( !empty($content) ) {
            $response = array(
                'success' => true,
                'content' => $content,
            );
            do_action( 'passster_validation_success', $input );
            return $response;
        } elseif ( 'on' === $options['toggle_ajax'] ) {
            $response = array(
                'success' => true,
            );
            do_action( 'passster_validation_success', $input );
            if ( $redirect ) {
                $response = array(
                    'success'  => true,
                    'redirect' => true,
                );
            }
            return $response;
        }
        
        return false;
    }
    
    /**
     * Hashing the password to store in a cookie.
     * @return void
     */
    public function hash_password()
    {
        // check nonce.
        
        if ( !wp_verify_nonce( $_POST['hash_nonce'], 'ps-hash-nonce' ) ) {
            print wp_json_encode( array(
                'success' => false,
                'error'   => 'Security check failed.',
            ) );
            exit;
        }
        
        // Check if input exists.
        
        if ( empty($_POST['password']) ) {
            print wp_json_encode( array(
                'success' => false,
                'error'   => 'No password provided.',
            ) );
            exit;
        }
        
        $response = array(
            'success'  => true,
            'password' => hash_hmac( 'sha256', esc_html( $_POST['password'] ), get_option( 'passster_secure_key' ) ),
        );
        print wp_json_encode( $response );
        exit;
    }
    
    /**
     * Enqueue scripts for shortcode
     *
     * @return void
     */
    public function add_public_scripts()
    {
        $suffix = ( defined( SCRIPT_DEBUG ) && SCRIPT_DEBUG ? '' : '.min' );
        $options = get_option( 'passster' );
        wp_enqueue_style(
            'passster-public',
            PASSSTER_URL . '/assets/public/passster-public' . $suffix . '.css',
            array(),
            PASSSTER_VERSION,
            'all'
        );
        wp_enqueue_script(
            'passster-cookie',
            PASSSTER_URL . '/assets/public/cookie.js',
            array( 'jquery' ),
            false,
            false
        );
        wp_enqueue_script(
            'passster-public',
            PASSSTER_URL . '/assets/public/passster-public' . $suffix . '.js',
            array( 'jquery', 'passster-cookie' ),
            PASSSTER_VERSION,
            false
        );
        $shortcodes = array();
        
        if ( isset( $options['third_party_shortcodes'] ) && !empty($options['third_party_shortcodes']) ) {
            $shortcodes_in_options = explode( ',', $options['third_party_shortcodes'] );
            if ( is_array( $shortcodes_in_options ) ) {
                foreach ( $shortcodes_in_options as $shortcode ) {
                    $shortcodes[$shortcode] = do_shortcode( str_replace( '{post-id}', get_the_id(), $shortcode ) );
                }
            }
        }
        
        $args = array(
            'ajax_url'     => admin_url() . 'admin-ajax.php',
            'nonce'        => wp_create_nonce( 'ps-password-nonce' ),
            'hash_nonce'   => wp_create_nonce( 'ps-hash-nonce' ),
            'logout_nonce' => wp_create_nonce( 'ps-logout-nonce' ),
            'post_id'      => get_the_id(),
            'shortcodes'   => $shortcodes,
            'permalink'    => get_permalink( get_the_id() ),
        );
        
        if ( isset( $options['cookie_duration_unit'] ) ) {
            $args['cookie_duration_unit'] = esc_html( $options['cookie_duration_unit'] );
        } else {
            $args['cookie_duration_unit'] = 'days';
        }
        
        
        if ( isset( $options['cookie_duration'] ) ) {
            $args['cookie_duration'] = esc_html( $options['cookie_duration'] );
        } else {
            $args['cookie_duration'] = 1;
        }
        
        
        if ( isset( $options['disable_cookie'] ) ) {
            $args['disable_cookie'] = esc_html( $options['disable_cookie'] );
        } else {
            $args['disable_cookie'] = false;
        }
        
        
        if ( isset( $options['unlock_mode'] ) ) {
            $args['unlock_mode'] = esc_html( $options['unlock_mode'] );
        } else {
            $args['unlock_mode'] = false;
        }
        
        wp_localize_script( 'passster-public', 'ps_ajax', $args );
        // if password type hint used.
        $password_typing = $options['show_password'];
        if ( $password_typing ) {
            wp_enqueue_script(
                'password-typing',
                PASSSTER_URL . '/assets/public/password-typing.js',
                array( 'jquery' ),
                PASSSTER_VERSION,
                false
            );
        }
    }

}