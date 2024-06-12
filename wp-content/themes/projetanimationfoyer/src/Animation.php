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
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts')); //test js
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_custom_styles' ) ); 
		add_filter( 'timber/context', array( $this, 'add_to_context' ) );
		add_filter( 'timber/twig', array( $this, 'add_to_twig' ) );
		add_action( 'init', array( $this, 'register_post_types' ) );
		add_action( 'init', array( $this, 'register_taxonomies' ) );
        add_action('init', array($this, 'setup_shortcodes'));
		// Ajout des hooks
		add_action('init', [$this, 'clear_all_transients']);
        add_action('wp_loaded', [$this, 'setup_front_page_context']);
        add_filter('the_content_more_link', array($this, 'modify_read_more_link'));
        add_action('wp_loaded', [$this, 'setup_benevoles_page_context']);
	}

	public function clear_all_transients() {
        global $wpdb;
        $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_%'");
        $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_site_transient_%'");
    }

	// Méthode pour configurer le contexte de la page d'accueil
    public function setup_front_page_context() {
        if (is_front_page()) {
            $context = Timber::context();
            $post = $context['post'];

            // Récupérer les catégories
            $categories = get_categories();

            // Récupérer les derniers articles de chaque catégorie
            $latest_posts_by_category = array();
            foreach ($categories as $category) {
                $args = array(
                    'posts_per_page' => 1,
                    'category_name' => $category->slug, // Utilisation du slug de la catégorie
                    'orderby' => 'date',
                    'order' => 'DESC',
                );
                $latest_posts = Timber::get_posts($args);
                if (!empty($latest_posts)) {
                    $latest_posts_by_category[$category->slug] = $latest_posts[0];
                }
            }

            // Afficher le contenu de $latest_posts_by_category pour vérification
            error_log(print_r($latest_posts_by_category, true));

            $context['latest_posts_by_category'] = $latest_posts_by_category;

            Timber::render(array('page-' . $post->post_name . '.twig', 'front-page.twig'), $context);
        }
    }

    // Ajoutez d'autres méthodes de votre classe Animation ici...


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
	
    function enqueue_scripts() {
        wp_enqueue_script('internal-script', get_template_directory_uri() . '/assets/js/main.js', array(), '1.0.0', true);

      }
      
      

    public function add_to_twig($twig) {
        // Ajoutez vos filtres et fonctions Twig personnalisés ici
        $twig->addFilter(new TwigFilter('myfoo', [$this, 'myfoo']));
		$twig->addFunction(new TwigFunction('home_url', function() {
            return home_url();		
        }));
		$twig->addExtension(new StringLoaderExtension());
        $twig->addFunction(new TwigFunction('do_shortcode', 'do_shortcode')); 
        // Ajouter un log pour confirmer l'ajout
        error_log('do_shortcode function added to Twig');
        
		
        return $twig;
    }

    // public function add_teaser_context($post_id) {
    //     $context = Timber::context();

    //     // Utilisez get_post_thumbnail_id pour récupérer l'ID de l'image mise en avant
    //     $thumbnail_id = get_post_thumbnail_id($post_id);
    //     if ($thumbnail_id) {
    //         // Chargez l'image via Timber et redimensionnez-la
    //         $context['teaser_image'] = new Timber\Image($thumbnail_id);
    //         $context['teaser_image_resized'] = $context['teaser_media'].resize(300, 200);  // Assurez-vous que la redimension est toujours disponible
    //     }

    //     return $context;
    // }

    // public function render_teaser($post_id) {
    //     $context = $this->add_teaser_context($post_id);
    //     Timber::render('parts/teaser.twig', $context);
    // }
	
    // Replaces the excerpt "Read More" text by a link
    public function modify_read_more_link() {
        // Remplacer "Your Read More Link Text" par le texte désiré en français
        return '<a class="more-link" href="' . get_permalink() . '">Lire la suite</a>';
    }

    public function myfoo( $text ) {
        $text .= ' bar!';
        return $text;
    }

    public function setup_shortcodes() {
        add_shortcode('current_year', function() {
            return date('Y');
        });
    }

    public function setup_benevoles_page_context() {
        if (is_page('benevoles')) {  // Make sure this matches the slug of your bénévoles page
            $context = Timber::context();
    
            // Set up arguments to query posts from the "bénévoles" category
            $args = [
                'category_name' => 'benevoles',  // Replace 'benevoles' with the exact slug of your category
                'posts_per_page' => -1  // Fetch all posts; adjust as necessary
            ];
            
            // Fetch the posts using Timber
            $context['benevoles_posts'] = Timber::get_posts($args);
    
            // Optionally, add any other contextual data you need for this page
            // For example, custom fields, additional texts, etc.
    
            // Render the template with context
            Timber::render('page-benevole.twig', $context);
        }
    }
}
