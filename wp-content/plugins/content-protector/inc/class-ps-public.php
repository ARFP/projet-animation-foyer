<?php

namespace passster;

use  Exception ;
class PS_Public
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
        add_shortcode( 'content_protector', array( $this, 'render_shortcode' ) );
        add_shortcode( 'passster', array( $this, 'render_shortcode' ) );
        add_filter( 'the_content', array( $this, 'filter_the_content' ) );
        add_filter( 'acf_the_content', array( $this, 'filter_the_content' ) );
        add_filter( 'get_the_excerpt', array( $this, 'filter_the_content' ) );
        add_action( 'template_redirect', array( $this, 'check_global_proctection' ) );
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
     * Render the Passster shortcode.
     *
     * @param array $atts array of attributes.
     * @param string|null $content the current content.
     *
     * @return string
     */
    public function render_shortcode( array $atts, string $content = null ) : string
    {
        // check if valid before restrict anything.
        $valid = PS_Conditional::is_valid( $atts );
        $options = get_option( 'passster' );
        if ( $valid ) {
            
            if ( !empty($atts['area']) ) {
                $area_id = esc_html( $atts['area'] );
                $area = get_post( $area_id );
                $content = $area->post_content;
                do_action( 'passster_content_unlocked' );
                return apply_filters( 'the_content', str_replace( '{post-id}', get_the_id(), $content ) );
            } else {
                $content = apply_filters( 'the_content', $content );
                do_action( 'passster_content_unlocked' );
                return apply_filters( 'passster_content', $content );
            }
        
        }
        // do nothing if no atts.
        if ( empty($atts) ) {
            return $content;
        }
        // Set default form.
        $form = PS_Form::get_password_form();
        // Password.
        
        if ( !empty($atts['password']) ) {
            $form = PS_Form::get_password_form();
            $form = str_replace( '[PASSSTER_TYPE]', 'password', $form );
        }
        
        // Area.
        
        if ( !empty($atts['area']) ) {
            $area_id = esc_html( $atts['area'] );
            $form = str_replace( '[PASSSTER_AREA]', $area_id, $form );
        }
        
        // Page.
        
        if ( !empty($atts['protection']) ) {
            $form = str_replace( '[PASSSTER_PROTECTION]', 'full', $form );
        } else {
            if ( !empty($atts['area']) ) {
                $form = str_replace( '[PASSSTER_PROTECTION]', 'area', $form );
            }
        }
        
        // Redirect.
        
        if ( !empty($atts['redirect']) ) {
            $form = str_replace( '[PASSSTER_REDIRECT]', esc_url( $atts['redirect'] ), $form );
        } else {
            $form = str_replace( '[PASSSTER_REDIRECT]', '', $form );
        }
        
        // headline.
        
        if ( !empty($atts['headline']) ) {
            $form = str_replace( '[PASSSTER_FORM_HEADLINE]', esc_html( $atts['headline'] ), $form );
        } else {
            $form = str_replace( '[PASSSTER_FORM_HEADLINE]', $options['headline'], $form );
        }
        
        // instruction.
        
        if ( !empty($atts['instruction']) ) {
            $form = str_replace( '[PASSSTER_FORM_INSTRUCTIONS]', esc_html( $atts['instruction'] ), $form );
        } else {
            $form = str_replace( '[PASSSTER_FORM_INSTRUCTIONS]', $options['instruction'], $form );
        }
        
        // placeholder.
        
        if ( !empty($atts['placeholder']) ) {
            $form = str_replace( '[PASSSTER_PLACEHOLDER]', esc_html( $atts['placeholder'] ), $form );
        } else {
            $form = str_replace( '[PASSSTER_PLACEHOLDER]', $options['placeholder'], $form );
        }
        
        // button.
        
        if ( !empty($atts['button']) ) {
            $form = str_replace( '[PASSSTER_BUTTON_LABEL]', esc_html( $atts['button'] ), $form );
        } else {
            $form = str_replace( '[PASSSTER_BUTTON_LABEL]', $options['button_label'], $form );
        }
        
        // modify id.
        
        if ( !empty($atts['id']) ) {
            $form = str_replace( '[PASSSTER_ID]', 'ps-' . esc_html( $atts['id'] ), $form );
        } else {
            $form = str_replace( '[PASSSTER_ID]', 'ps-' . wp_rand( 10, 1000 ), $form );
        }
        
        // hide or not.
        
        if ( !empty($atts['hide']) ) {
            $form = str_replace( '[PASSSTER_HIDE]', ' passster-hide', $form );
        } else {
            $form = str_replace( '[PASSSTER_HIDE]', '', $form );
        }
        
        // ACF field.
        
        if ( !empty($atts['acf']) ) {
            $form = str_replace( '[PASSSTER_ACF]', ' data-acf="' . esc_html( $atts['acf'] ) . '"', $form );
        } else {
            $form = str_replace( '[PASSSTER_ACF]', '', $form );
        }
        
        return $form;
    }
    
    /**
     * Filters the_content with Passster.
     *
     * @param string $content given content.
     *
     * @return string
     * @throws Exception
     */
    public function filter_the_content( string $content ) : string
    {
        $post_id = get_the_id();
        $activate_protection = get_post_meta( $post_id, 'passster_activate_protection', true );
        // user restriction.
        $user_restriction_type = get_post_meta( $post_id, 'passster_user_restriction_type', true );
        $user_restriction = get_post_meta( $post_id, 'passster_user_restriction', true );
        // Redirection.
        $redirection = get_post_meta( $post_id, 'passster_redirect_url', true );
        // texts.
        $headline = get_post_meta( $post_id, 'passster_headline', true );
        $instruction = get_post_meta( $post_id, 'passster_instruction', true );
        $placeholder = get_post_meta( $post_id, 'passster_placeholder', true );
        $button = get_post_meta( $post_id, 'passster_button', true );
        $id = get_post_meta( $post_id, 'passster_id', true );
        if ( !$activate_protection ) {
            return $content;
        }
        // build atts array to validate.
        $atts = array();
        $shortcode = '';
        $password = get_post_meta( $post_id, 'passster_password', true );
        $atts['password'] = $password;
        $shortcode = '[passster password="' . $password . '" protection="full" ';
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
        $shortcode .= ']{content}[/passster]';
        // check if valid before restrict anything.
        $valid = PS_Conditional::is_valid( $atts );
        if ( $valid ) {
            return $content;
        }
        // replace placeholder with content.
        $shortcode = str_replace( '{content}', $content, $shortcode );
        return do_shortcode( $shortcode );
    }
    
    /**
     * Redirect if global protection is activated and no password is set.
     *
     * @return void
     * @throws Exception
     */
    public function check_global_proctection()
    {
        $options = get_option( 'passster' );
        // Allow Elementor editing the page.
        $elementor_preview = filter_input( INPUT_GET, 'elementor-preview', FILTER_SANITIZE_STRING );
        if ( $elementor_preview ) {
            return;
        }
        // Allow Live Canvas Editor.
        $live_canvas_preview = filter_input( INPUT_GET, 'lc_action_launch_editing', FILTER_SANITIZE_STRING );
        if ( $live_canvas_preview ) {
            return;
        }
        if ( !isset( $options['global_protection_id'] ) ) {
            return;
        }
        if ( !isset( $options['activate_global_protection'] ) ) {
            return;
        }
        // Build $atts array based on protection settings.
        $post_id = esc_html( $options['global_protection_id'] );
        $is_active = esc_html( $options['activate_global_protection'] );
        $atts = array();
        $password = get_post_meta( $post_id, 'passster_password', true );
        $atts['password'] = esc_html( $password );
        if ( !empty($post_id) ) {
            
            if ( $is_active ) {
                if ( is_page( $post_id ) || is_single( $post_id ) ) {
                    return;
                }
                // Check excluded pages.
                if ( isset( $options['exclude_pages'] ) ) {
                    foreach ( $options['exclude_pages'] as $excluded_page_id ) {
                        if ( is_page( $excluded_page_id ) ) {
                            return;
                        }
                    }
                }
                // Check if cookie is set.
                
                if ( empty($_COOKIE['passster']) || !PS_Conditional::is_valid( $atts ) ) {
                    $global_protection_url = get_permalink( $post_id );
                    wp_redirect( esc_url_raw( $global_protection_url ) );
                    exit;
                }
            
            }
        
        }
    }

}