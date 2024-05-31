<?php

namespace Projet;

use Timber\Timber;
use Timber\Site;
use Timber\Menu;
use Twig\Extension\StringLoaderExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class Animation extends Site {
	
    public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'theme_supports' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_custom_styles' ) ); // Appel dans le bon crochet
		add_filter( 'timber/context', array( $this, 'add_to_context' ) );
		add_filter( 'timber/twig', array( $this, 'add_to_twig' ) );
		add_action( 'init', array( $this, 'register_post_types' ) );
		add_action( 'init', array( $this, 'register_taxonomies' ) );
	}

    public function register_post_types() {
        // Définissez ici vos types de contenu personnalisés si nécessaire
    }

    public function register_taxonomies() {
        // Définissez ici vos taxonomies personnalisées si nécessaire
    }

    public function add_to_context( $context ) {
        // Ajoutez des éléments au contexte Timber ici
        $context['foo'] = 'bar';
        $context['stuff'] = 'I am a value set in your functions.php file';
        $context['notes'] = 'These values are available everytime you call Timber::context();';
        $context['menu']  = Timber::get_menu('Menu Principal');
        $context['menu_secondaire']  = Timber::get_menu('Menu Secondaire');
        $context['logo_url'] = get_stylesheet_directory_uri() . '/assets/img/logo-centre-de-readaptation-de-mulhouse-colors.svg';
        $context['site'] = $this;
        return $context;
    }

	public function theme_supports() {
		// Ajoutez les supports de thème WordPress ici
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support(
			'html5', array(
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
			)
		);
		add_theme_support(
			'post-formats', array(
				'aside',
				'image',
				'video',
				'quote',
				'link',
				'gallery',
				'audio',
			)
		);
		add_theme_support( 'menus' );
	}
	
	public function enqueue_custom_styles() {
		// Enregistrez votre feuille de style CSS dans le bon crochet
		wp_enqueue_style( 'main-style', get_stylesheet_directory_uri() . '/assets/css/main.css' );
	}
	

    public function add_to_twig($twig) {
        // Ajoutez vos filtres et fonctions Twig personnalisés ici
        $twig->addFilter(new TwigFilter('myfoo', [$this, 'myfoo']));
		$twig->addFunction(new TwigFunction('home_url', function() {
            return home_url();
        }));

        return $twig;
    }

    public function myfoo( $text ) {
        $text .= ' bar!';
        return $text;
    }
}
