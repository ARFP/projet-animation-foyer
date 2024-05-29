<?php

namespace Projet;
// error_reporting(E_ALL);
// ini_set('display_errors', TRUE);
// ini_set('display_startup_errors', TRUE);
// require_once __DIR__ . '/vendor/autoload.php';
use Timber\Timber;
use Timber\Site;
use Timber\Menu;
use Twig\Extension\StringLoaderExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;


class Animation extends Site {
	

	
		public function __construct() {
			add_action( 'after_setup_theme', array( $this, 'theme_supports' ) );
			add_filter( 'timber/context', array( $this, 'add_to_context' ) );
			add_filter( 'timber/twig', array( $this, 'add_to_twig' ) );
			add_action( 'init', array( $this, 'register_post_types' ) );
			add_action( 'init', array( $this, 'register_taxonomies' ) );
			parent::__construct();
		}

		
		/** This is where you can register custom post types. */
		public function register_post_types() {
	
		}
		/** This is where you can register custom taxonomies. */
		public function register_taxonomies() {
	
		}

		
	
		/** This is where you add some context
		 *
		 * @param string $context context['this'] Being the Twig's {{ this }}.
		 */
		public function add_to_context( $context ) {
			$context['foo'] = 'bar';
			$context['stuff'] = 'I am a value set in your functions.php file';
			$context['notes'] = 'These values are available everytime you call Timber::context();';
			$context['menu']  = Timber::get_menu('Menu Principal');
			$context['menu_secondaire']  = Timber::get_menu('Menu Secondaire');
			$context['site'] = $this;
			return $context;
		}
	
		public function extend_context( $context ) {
			// Appel de la méthode parente pour inclure le contexte existant
			$context = parent::add_to_context( $context );
	
			// Construire l'URL de l'image
			$context['logo_url'] = get_stylesheet_directory_uri() . '/views/img/crmlogo1.jpg';
	
			return $context;
		}
		public function theme_supports() {
			// Add default posts and comments RSS feed links to head.
			add_theme_support( 'automatic-feed-links' );
	
			/*
			 * Let WordPress manage the document title.
			 * By adding theme support, we declare that this theme does not use a
			 * hard-coded <title> tag in the document head, and expect WordPress to
			 * provide it for us.
			 */
			add_theme_support( 'title-tag' );
	
			/*
			 * Enable support for Post Thumbnails on posts and pages.
			 *
			 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
			 */
			add_theme_support( 'post-thumbnails' );
	
			/*
			 * Switch default core markup for search form, comment form, and comments
			 * to output valid HTML5.
			 */
			add_theme_support(
				'html5', array(
					'comment-form',
					'comment-list',
					'gallery',
					'caption',
				)
			);
	
			/*
			 * Enable support for Post Formats.
			 *
			 * See: https://codex.wordpress.org/Post_Formats
			 */
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
	
		/** This Would return 'foo bar!'.
		 *
		 * @param string $text being 'foo', then returned 'foo bar!'.
		 */
		public function myfoo( $text ) {
			$text .= ' bar!';
			return $text;
		}
	
		/** This is where you can add your own functions to twig.
		 *
		 * @param string $twig get extension.
		 */
		public function add_to_twig($twig)
		{
			/**
			 * Required when you want to use Twig’s template_from_string.
			 * @link https://twig.symfony.com/doc/3.x/functions/template_from_string.html
			 */
			// $twig->addExtension( new Twig\Extension\StringLoaderExtension() );
	
			$twig->addFilter(new TwigFilter('myfoo', [$this, 'myfoo']));
			
			// Add your custom function enqueue_style to Twig
			$enqueue_style = new TwigFunction('enqueue_style', function ($handle, $src) {
				wp_enqueue_style( $handle, get_stylesheet_directory_uri() . '/assets/css/tailwind.css' . $src );
			});
	
			$twig->addFunction($enqueue_style);
	
			return $twig;
		}

}

