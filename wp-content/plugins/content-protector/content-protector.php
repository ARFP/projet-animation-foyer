<?php

/**
 * Plugin Name:       Passster
 * Plugin URI:        https://patrickposner.dev
 * Description:       A simple plugin to password-protect your complete website, some pages/posts or just parts of your content.
 * Version:           4.2.6.5
 * Author:            Patrick Posner
 * Author URI:        https://patrickposner.dev
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       content-protector
 * Domain Path:       /languages
 *
 */
define( 'PASSSTER_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'PASSSTER_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'PASSSTER_VERSION', '4.2.6.5' );
// run plugin.

if ( !function_exists( 'passster_run_plugin' ) ) {
    add_action( 'plugins_loaded', 'passster_run_plugin' );
    /**
     * Run plugin
     *
     * @return void
     * @throws Exception
     */
    function passster_run_plugin()
    {
        // Include files.
        require_once PASSSTER_PATH . '/inc/class-ps-conditional.php';
        require_once PASSSTER_PATH . '/inc/class-ps-helper.php';
        require_once PASSSTER_PATH . '/inc/class-ps-form.php';
        require_once PASSSTER_PATH . '/inc/class-ps-ajax.php';
        require_once PASSSTER_PATH . '/inc/class-ps-public.php';
        require_once PASSSTER_PATH . '/inc/class-ps-migrator.php';
        require_once PASSSTER_PATH . '/inc/class-ps-block-editor.php';
        require_once PASSSTER_PATH . '/inc/class-ps-rest-handler.php';
        // admin.
        require_once PASSSTER_PATH . '/inc/admin/inc/class-ps-admin-settings.php';
        require_once PASSSTER_PATH . '/inc/admin/inc/class-ps-admin.php';
        require_once PASSSTER_PATH . '/inc/admin/inc/class-ps-meta.php';
        require_once PASSSTER_PATH . '/inc/admin/inc/class-ps-dynamic-styles.php';
        // load Freemius.
        require_once PASSSTER_PATH . '/inc/freemius-setup.php';
        // localize.
        $textdomain_dir = plugin_basename( dirname( __FILE__ ) ) . '/languages';
        load_plugin_textdomain( 'content-protector', false, $textdomain_dir );
        if ( !get_option( 'passster_secure_key' ) ) {
            add_option( 'passster_secure_key', bin2hex( random_bytes( 32 ) ) );
        }
        passster\PS_Admin::get_instance();
        passster\PS_Admin_Settings::get_instance();
        passster\PS_Meta::get_instance();
        passster\PS_Dynamic_Styles::get_instance();
        passster\PS_Form::get_instance();
        passster\PS_Ajax::get_instance();
        passster\PS_Public::get_instance();
        passster\PS_Block_Editor::get_instance();
        passster\PS_Rest_Handler::get_instance();
        // Maybe migrate settings.
        $options = get_option( 'passster' );
        if ( empty($options) ) {
            passster\PS_Migrator::migrate();
        }
    }

}
