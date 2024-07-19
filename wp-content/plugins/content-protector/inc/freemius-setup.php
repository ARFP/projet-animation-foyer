<?php

if ( !function_exists( 'passster_fs' ) ) {
    // Create a helper function for easy SDK access.
    function passster_fs()
    {
        global  $passster_fs ;
        
        if ( !isset( $passster_fs ) ) {
            // Activate multisite network integration.
            if ( !defined( 'WP_FS__PRODUCT_1938_MULTISITE' ) ) {
                define( 'WP_FS__PRODUCT_1938_MULTISITE', true );
            }
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/freemius/start.php';
            $passster_fs = fs_dynamic_init( array(
                'id'              => '1938',
                'slug'            => 'content-protector',
                'type'            => 'plugin',
                'public_key'      => 'pk_9d9d6d17bd34372b199f36e37dd4b',
                'is_premium'      => false,
                'premium_suffix'  => '',
                'has_addons'      => false,
                'has_paid_plans'  => true,
                'has_affiliation' => 'selected',
                'menu'            => array(
                'slug'        => 'edit.php?post_type=protected_areas',
                'contact'     => false,
                'support'     => false,
                'affiliation' => false,
            ),
                'is_live'         => true,
            ) );
        }
        
        return $passster_fs;
    }
    
    // Init Freemius.
    passster_fs();
    // Signal that SDK was initiated.
    do_action( 'passster_fs_loaded' );
}

function passster_fs_settings_url()
{
    return admin_url( 'admin.php?page=passster-settings' );
}

passster_fs()->add_filter( 'connect_url', 'passster_fs_settings_url' );
passster_fs()->add_filter( 'after_skip_url', 'passster_fs_settings_url' );
passster_fs()->add_filter( 'after_connect_url', 'passster_fs_settings_url' );
passster_fs()->add_filter( 'after_pending_connect_url', 'passster_fs_settings_url' );
passster_fs()->add_filter( 'show_deactivation_subscription_cancellation', '__return_false' );
passster_fs()->add_filter( 'show_deactivation_feedback_form', '__return_false' );
/**
 * Remove freemius pages.
 *
 * @param bool $is_visible indicates if visible or not.
 * @param int $submenu_id current submenu id.
 *
 * @return bool
 */
function passster_is_submenu_visible( $is_visible, $submenu_id )
{
    return false;
}

passster_fs()->add_filter(
    'is_submenu_visible',
    'passster_is_submenu_visible',
    10,
    2
);
/**
 * Add custom icon for Freemius.
 *
 * @return string
 */
passster_fs()->add_filter( 'plugin_icon', function () {
    return PASSSTER_PATH . '/assets/admin/passster-icon.png';
} );
passster_fs()->add_action( 'after_uninstall', function () {
    global  $wpdb ;
    // Delete option.
    
    if ( is_multisite() ) {
        delete_site_option( 'passster' );
    } else {
        delete_option( 'passster' );
    }
    
    // Delete tables.
    $tables = [ $wpdb->prefix . "passster_concurrent_logins", $wpdb->prefix . "passster_statistics" ];
    foreach ( $tables as $table ) {
        $wpdb->query( "DROP TABLE IF EXISTS {$table}" );
    }
    // Delete all password lists.
    $wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type='password_lists'" );
    // Delete all protected areas.
    $wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type='protected_areas'" );
    // Delete all assigned meta
    $wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE 'passster_%'" );
} );