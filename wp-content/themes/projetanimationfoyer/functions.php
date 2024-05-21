<?php 
namespace App;

use Timber\Timber;

// Load Composer dependencies.
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/ProjetAnimationFoyer.php';

Timber::init();

new ProjetAnimationFoyer();

/*
// Ajouter la prise en charge des images mises en avant
add_theme_support( 'post-thumbnails' );

// Ajouter automatiquement le titre du site dans l'en-tête du site
add_theme_support( 'title-tag' );

function crm_register_assets() {
    
    // Déclarer jQuery
    wp_enqueue_script('jquery' );
    
    // Déclarer le JS
	wp_enqueue_script( 
        'crm', 
        get_template_directory_uri() . '/js/script.js', 
        array( 'jquery' ), 
        '1.0', 
        true
    );  
  	
    // Déclarer le fichier CSS à un autre emplacement
    wp_enqueue_style( 
        'crm', 
        get_template_directory_uri() . '/assets/css/main.css',
        array(), 
        '1.0'
    );
}
add_action( 'wp_enqueue_scripts', 'crm_register_assets' );

    // Déclarations des Menu
register_nav_menus( array(
	'main' => 'Menu Principal',
	'footer' => 'Bas de page',
) );


register_sidebar( array(
	'id' => 'blog-sidebar',
	'name' => 'Blog',
) );








// Initialisation de Timber

// $timber = new \Timber\Timber();
// \Timber\Timber::$autoescape = false;
// \Timber\Timber::$dirname = ['templates', 'views'];

// function wporg_my_excerpt_protected( $excerpt ) {
//     if ( post_password_required() )
//         $excerpt = '<em>[Protégé par mot de passe]</em>';
//     return $excerpt;
// }
// add_filter( 'the_excerpt', 'wporg_my_excerpt_protected' );


// // Masquer les publications protégées
// function wporg_exclude_protected($where) {
// 	global $wpdb;
// 	return $where .= " AND {$wpdb->posts}.post_password = '' ";
// }

// Choisir les fichiers modèles concernés
// function wporg_exclude_protected_action($query) {
// 	if( !is_single() && !is_page() && !is_admin() ) {
// 		add_filter( 'posts_where', 'wporg_exclude_protected' );
// 	}
// }

// Ajouter l’action sur le bon crochet d’action
// add_action('pre_get_posts', 'wporg_exclude_protected_action');


// function wporg_my_password_form() {
//     global $post;
//     $label = 'pwbox-'.( empty( $post->ID ) ? rand() : $post->ID );
//     $o = '<form action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" method="post">
//     Pour voir cette publication, saisissez le mot de passe :
//     <label for="' . $label . '">Mot de passe :</label>
//     <input name="post_password" id="' . $label . '" type="password" size="20" maxlength="20" /><input type="submit" name="Submit" value="OK" />
//     </form>
//     ';
//     return $o;
// }
// add_filter( 'the_password_form', 'wporg_my_password_form' );

*/
