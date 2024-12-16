<?php

namespace passster;

class PS_Form
{
    /**
     * Contains instance or null
     *
     * @var object|null
     */
    private static  $instance = null ;
    /**
     * Returns instance of PS_Form.
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
     * Get password form markup and replace placeholders
     *
     * @return string
     */
    public static function get_password_form() : string
    {
        $options = get_option( 'passster' );
        ob_start();
        include apply_filters( 'passster_password_form', PASSSTER_PATH . '/inc/templates/password-form.php' );
        $password_form = ob_get_contents();
        ob_end_clean();
        $password_form_placeholders = array(
            '[PASSSTER_AUTH]'  => 'passster_password',
            '[PS_AJAX_LOADER]' => PASSSTER_URL . '/assets/public/ps-loader.svg',
        );
        $show_password = $options['show_password'];
        
        if ( $show_password ) {
            $password_form_placeholders['[PASSSTER_SHOW_PASSWORD]'] = '<label for="passster-password-hint"><input name="passster-password-hint" id="passster-password-hint" type="checkbox" onclick="showPassword()"/> ' . esc_html__( 'Show Password', 'content-protector' ) . '</label>';
        } else {
            $password_form_placeholders['[PASSSTER_SHOW_PASSWORD]'] = '';
        }
        
        foreach ( $password_form_placeholders as $placeholder => $string ) {
            $password_form = str_replace( $placeholder, $string, $password_form );
        }
        return $password_form;
    }

}