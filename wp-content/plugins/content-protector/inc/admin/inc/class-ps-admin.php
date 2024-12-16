<?php

namespace passster;

class PS_Admin
{
    /**
     * Contains instance or null
     *
     * @var object|null
     */
    private static  $instance = null ;
    /**
     * Returns instance of PS_Admin.
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
    
    public function __construct()
    {
        // Check permissions before include settings.
        add_action( 'admin_menu', array( $this, 'remove_custom_fields_metabox' ) );
        add_action( 'init', array( $this, 'register_password_areas' ) );
        add_filter( 'manage_protected_areas_posts_columns', array( $this, 'set_area_columns' ) );
        add_filter( 'manage_post_posts_columns', array( $this, 'set_post_columns' ) );
        add_filter( 'manage_page_posts_columns', array( $this, 'set_post_columns' ) );
        add_action(
            'manage_post_posts_custom_column',
            array( $this, 'set_post_columns_content' ),
            10,
            2
        );
        add_action(
            'manage_page_posts_custom_column',
            array( $this, 'set_post_columns_content' ),
            10,
            2
        );
        add_action(
            'manage_protected_areas_posts_custom_column',
            array( $this, 'set_area_columns_content' ),
            10,
            2
        );
    }
    
    /**
     * Remove Custom Fields meta box
     */
    public function remove_custom_fields_metabox()
    {
        $post_types = get_post_types( array(
            'public'              => true,
            'exclude_from_search' => false,
        ), 'names' );
        remove_meta_box( 'postcustom', $post_types, 'normal' );
    }
    
    /**
     * Register post type "protected areas"
     *
     * @return void
     */
    public function register_password_areas()
    {
        $labels = array(
            'name'               => _x( 'Protected Areas', 'post type general name', 'content-protector' ),
            'singular_name'      => _x( 'Protected Area', 'post type singular name', 'content-protector' ),
            'menu_name'          => _x( 'Protected Areas', 'admin menu', 'content-protector' ),
            'name_admin_bar'     => _x( 'Protected Area', 'add new on admin bar', 'content-protector' ),
            'add_new'            => _x( 'Add New', 'content-protector' ),
            'add_new_item'       => __( 'Add New Protected Area', 'content-protector' ),
            'new_item'           => __( 'New Protected Area', 'content-protector' ),
            'edit_item'          => __( 'Edit Protected Area', 'content-protector' ),
            'view_item'          => __( 'View Protected Area', 'content-protector' ),
            'all_items'          => __( 'Protected Areas', 'content-protector' ),
            'search_items'       => __( 'Search Protected Areas', 'content-protector' ),
            'parent_item_colon'  => __( 'Parent Protected Area', 'content-protector' ),
            'not_found'          => __( 'No Protected Areas found.', 'content-protector' ),
            'not_found_in_trash' => __( 'No Protected Areas found in Trash.', 'content-protector' ),
        );
        $args = array(
            'labels'             => $labels,
            'description'        => __( 'Manageable protected areas', 'content-protector' ),
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => 'passster',
            'query_var'          => true,
            'rewrite'            => false,
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array( 'title', 'editor' ),
            'show_in_rest'       => true,
        );
        
        if ( current_user_can( 'administrator' ) && (is_plugin_active( 'elementor/elementor.php' ) || is_plugin_active( 'fusion-builder/fusion-builder.php' ) || is_plugin_active( 'livecanvas/livecanvas-plugin-index.php' ) || is_plugin_active( 'divi-builder/divi-builder.php' ) || is_plugin_active( 'oxygen/functions.php' )) ) {
            $args['public'] = true;
            $args['publicly_queryable'] = true;
        }
        
        $theme = wp_get_theme();
        
        if ( current_user_can( 'administrator' ) && 'Divi' == $theme->name || 'Divi' == $theme->parent_theme ) {
            $args['public'] = true;
            $args['publicly_queryable'] = true;
        }
        
        register_post_type( 'protected_areas', $args );
    }
    
    /**
     * Set column headers for pages/post
     *
     * @param array $columns array of columns.
     *
     * @return array
     */
    public function set_post_columns( array $columns ) : array
    {
        $columns['protected'] = __( 'Password', 'content-protector' );
        return $columns;
    }
    
    /**
     * Add content to registered columns for posts/pages
     *
     * @param string $column name of the column.
     * @param int $post_id current id.
     *
     * @return void
     */
    public function set_post_columns_content( string $column, int $post_id )
    {
        switch ( $column ) {
            case 'protected':
                $protected = get_post_meta( $post_id, 'passster_activate_protection', true );
                
                if ( $protected ) {
                    $protection_type = get_post_meta( $post_id, 'passster_protection_type', true );
                    $password = get_post_meta( $post_id, 'passster_password', true );
                    echo  esc_html( $password ) ;
                }
                
                break;
        }
    }
    
    /**
     * Set column headers password lists post type
     *
     * @param array $columns array of columns.
     *
     * @return array
     */
    public function set_area_columns( array $columns ) : array
    {
        $columns['shortcode'] = __( 'Shortcode', 'content-protector' );
        $columns['protection-type'] = __( 'Protection Type', 'content-protector' );
        $columns['password'] = __( 'Password', 'content-protector' );
        $columns['redirect'] = __( 'Redirect', 'content-protector' );
        unset( $columns['date'] );
        return $columns;
    }
    
    /**
     * Add content to registered columns for password list post type.
     *
     * @param string $column name of the column.
     * @param int $post_id current id.
     *
     * @return void
     */
    public function set_area_columns_content( string $column, int $post_id )
    {
        switch ( $column ) {
            case 'shortcode':
                $shortcode = get_post_meta( $post_id, 'passster_area_shortcode', true );
                ?>
				<?php 
                
                if ( !empty($shortcode) ) {
                    ?>
                <button type="button"
                        class="components-button components-clipboard-button is-primary area-<?php 
                    echo  esc_html( $post_id ) ;
                    ?>"
                        data-shortcode="<?php 
                    echo  esc_html( $shortcode ) ;
                    ?>"
                        onclick="copyShortcode(this)"
                >
					<?php 
                    esc_html_e( 'Copy Shortcode', 'content-protector' );
                    ?>
                </button>
                <script>
                    function copyShortcode(element) {
                        // Copy to clipboard.
                        navigator.clipboard.writeText(element.getAttribute('data-shortcode')).then(function () {
                            element.innerHTML = "<?php 
                    esc_html_e( 'Copied!', 'content-protector' );
                    ?>";

                            setTimeout(function () {
                                element.innerHTML = "<?php 
                    esc_html_e( 'Copy Shortcode', 'content-protector' );
                    ?>"
                            }, 1500);

                        }, function (err) {
                            console.error('Could not copy shortcode: ', err);
                        });
                    }
                </script>
                <style>
                    button {
                        white-space: nowrap;
                        text-shadow: none;
                        outline: 1px solid transparent;
                        width: auto;
                        display: inline-flex;
                        text-decoration: none;
                        font-family: inherit;
                        font-weight: normal;
                        font-size: 13px;
                        margin: 0;
                        border: 0;
                        cursor: pointer;
                        -webkit-appearance: none;
                        background: #007cba;
                        transition: box-shadow 0.1s linear;
                        height: 36px;
                        align-items: center;
                        box-sizing: border-box;
                        padding: 6px 12px;
                        border-radius: 2px;
                        color: #fff;
                    }

                    button:hover {
                        background: #006ba1;
                    }
                </style>
			<?php 
                }
                
                ?>
				<?php 
                break;
            case 'protection-type':
                $type = get_post_meta( $post_id, 'passster_protection_type', true );
                $protection_types = array(
                    'password'       => __( 'Password', 'content-protector' ),
                    'password_list'  => __( 'Password List', 'content-protector' ),
                    'password_lists' => __( 'Password Lists', 'content-protector' ),
                    'passwords'      => __( 'Passwords', 'content-protector' ),
                    'recaptcha'      => __( 'ReCaptcha', 'content-protector' ),
                );
                echo  esc_html( $protection_types[$type] ) ;
                break;
            case 'password':
                $type = get_post_meta( $post_id, 'passster_protection_type', true );
                $protection_types = array(
                    'password'       => get_post_meta( $post_id, 'passster_password', true ),
                    'password_list'  => get_post_meta( $post_id, 'passster_password_list', true ),
                    'password_lists' => get_post_meta( $post_id, 'passster_password_lists', true ),
                    'passwords'      => get_post_meta( $post_id, 'passster_passwords', true ),
                    'recaptcha'      => '-',
                );
                // Build the edit link if it's a password list.
                
                if ( 'password_list' === $type ) {
                    $edit_link = get_edit_post_link( $protection_types[$type] );
                    ?>
                    <a target="_blank"
                       href="<?php 
                    echo  esc_url( $edit_link ) ;
                    ?>"><?php 
                    echo  esc_html( $protection_types[$type] ) ;
                    ?></a>
					<?php 
                } elseif ( 'password_lists' === $type ) {
                    $password_list_ids = get_post_meta( $post_id, 'passster_password_lists', true );
                    $lists_string = '';
                    foreach ( $password_list_ids as $password_list_id ) {
                        $edit_url = admin_url( 'post.php?post=' . esc_html( $password_list_id ) . '&action=edit', 'https' );
                        $lists_string .= '<a href="' . esc_url( $edit_url ) . '">' . esc_html( $password_list_id ) . '</a> | ';
                    }
                    echo  rtrim( $lists_string, " |" ) ;
                } else {
                    echo  esc_html( $protection_types[$type] ) ;
                }
                
                break;
            case 'user-restriction':
                $restriction = get_post_meta( $post_id, 'passster_activate_user_restriction', true );
                $type = get_post_meta( $post_id, 'passster_user_restriction_type', true );
                $value = get_post_meta( $post_id, 'passster_user_restriction', true );
                // Get available user roles.
                global  $wp_roles ;
                $roles = $wp_roles->roles;
                
                if ( $restriction ) {
                    
                    if ( 'user-role' === $type ) {
                        echo  esc_html( $roles[$value]['name'] ) ;
                    } elseif ( 'username' === $type ) {
                        echo  esc_html( $value ) ;
                    } else {
                        echo  '-' ;
                    }
                
                } else {
                    echo  '-' ;
                }
                
                break;
            case 'redirect':
                $redirect = get_post_meta( $post_id, 'passster_redirect_url', true );
                
                if ( !empty($redirect) ) {
                    echo  '<a href="' . esc_url( $redirect ) . '">' . esc_url( $redirect ) . '</a>' ;
                } else {
                    echo  '-' ;
                }
                
                break;
        }
    }

}