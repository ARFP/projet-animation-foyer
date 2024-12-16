<?php

namespace passster;

use  Exception ;
class PS_Conditional
{
    /**
     * Check if valid authentication exists.
     *
     * @param array $atts array of attributes.
     *
     * @return boolean
     * @throws Exception
     */
    public static function is_valid( array $atts ) : bool
    {
        $input = '';
        // is Cookie set?
        if ( !empty($_COOKIE['passster']) ) {
            $input = esc_html( $_COOKIE['passster'] );
        }
        // Valid password.
        if ( self::is_valid_password( $input, $atts ) ) {
            return true;
        }
        // captcha.
        if ( isset( $atts['captcha'] ) ) {
            if ( 'captcha' == $input ) {
                return true;
            }
        }
        // if nothing was correct.
        return false;
    }
    
    /**
     * Validate the password.
     *
     * @param string $input given password to validate.
     * @param array $atts given arguments to check.
     *
     * @return bool
     * @throws Exception
     */
    public static function is_valid_password( string $input, array $atts ) : bool
    {
        // password.
        
        if ( !empty($atts['password']) ) {
            $hash = hash_hmac( 'sha256', esc_html( $atts['password'] ), get_option( 'passster_secure_key' ) );
            if ( hash_equals( $hash, $input ) ) {
                return true;
            }
        }
        
        return false;
    }

}