<?php

namespace passster;

use  Exception ;
class PS_Helper
{
    /**
     * Get string between two strings.
     *
     * @param string $string string to find.
     * @param string $start start string.
     * @param string $end end string.
     *
     * @return string
     */
    public static function get_string_between( string $string, string $start, string $end ) : string
    {
        $string = ' ' . $string;
        $ini = strpos( $string, $start );
        if ( $ini == 0 ) {
            return '';
        }
        $ini += strlen( $start );
        $len = strpos( $string, $end, $ini ) - $ini;
        return substr( $string, $ini, $len );
    }
    
    /**
     * Preg matches shortcode and return cleaned output.
     *
     * @param string $content given content.
     *
     * @return string
     */
    public static function get_shortcode_content( string $content, $password ) : string
    {
        preg_match( '/\\[passster.*password="' . $password . '".*?\\]/', $content, $matches );
        $string = '';
        if ( isset( $matches ) && !empty($matches) ) {
            foreach ( $matches as $match ) {
                $string = self::get_string_between( $content, $match, '[/passster]' );
            }
        }
        return $string;
    }

}