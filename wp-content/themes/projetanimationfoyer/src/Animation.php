<?php

namespace Projet;

use Timber\Timber;
use Timber\Site;
use Timber\Menu;
use Twig\Extension\StringLoaderExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class Animation extends Site {
      // ...

    /**
     * Vérifie le mot de passe de l'utilisateur.
     *
     * @param string $user_login Le login de l'utilisateur.
     * @param string $password Le mot de passe saisi.
     * @return bool Retourne true si le mot de passe est correct.
     */
    public function verify_user_password($user_login, $password) {
        $user = get_user_by('login', $user_login);
        if ($user && wp_check_password($password, $user->user_pass, $user->ID)) {
            return true;
        }
        return false;
    }
 public function __construct() {
        parent::__construct();
        add_action('init', array($this, 'clear_all_transients'));
        add_action('init', array($this, 'start_session'), 1);
        add_action('wp_logout', array($this, 'end_session'));
        add_action('wp_login', array($this, 'login_redirect'), 10, 2);
        add_action('after_setup_theme', array($this, 'theme_supports'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_custom_styles'));
        add_filter('timber/context', array($this, 'add_to_context'));
        add_filter('timber/twig', array($this, 'add_to_twig'));
        add_action('init', array($this, 'register_post_types'));
        add_action('init', array($this, 'register_taxonomies'));
        add_action('init', array($this, 'setup_shortcodes'));
        add_action('wp_loaded', array($this, 'setup_front_page_context'));
        add_action('wp_loaded', array($this, 'setup_benevoles_page_context'));
        add_filter('the_content_more_link', array($this, 'modify_read_more_link'));
    }

    public function start_session() {
        if (!session_id()) {
            session_start();
        }
    }
    
    public function end_session() {
        if (session_id()) {
            session_destroy();
        }
    }
    

    public function clear_all_transients() {
        global $wpdb;
        $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_%'");
        $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_site_transient_%'");
    }


    public function login_redirect($user_login, $user) {
        if (isset($_POST['pwd'])) {
            $password = sanitize_text_field($_POST['pwd']);
            if (wp_check_password($password, $user->user_pass, $user->ID)) {
                wp_redirect(home_url('/page-secrete'));
                exit;
            } else {
                wp_redirect(home_url('/accieul'));
                exit;
            }
        }
    }
    

    public function theme_supports() {
        add_theme_support('automatic-feed-links');
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
        add_theme_support('html5', array(
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
        ));
        add_theme_support('post-formats', array(
            'aside',
            'image',
            'video',
            'quote',
            'link',
            'gallery',
            'audio',
        ));
        add_theme_support('menus');
    }

    public function enqueue_scripts() {
        wp_enqueue_script('internal-script', get_template_directory_uri() . '/assets/js/main.js', array(), '1.0.0', true);
    }

    public function enqueue_custom_styles() {
        wp_enqueue_style('main-style', get_stylesheet_directory_uri() . '/assets/css/main.css');
    }

    public function add_to_context($context) {
        $context['menu'] = Timber::get_menu('Menu Principal');
        $context['menu_secondaire'] = Timber::get_menu('Menu Secondaire');
        $context['logo_url'] = get_stylesheet_directory_uri() . '/assets/img/logo-centre-de-readaptation-de-mulhouse-colors.svg';
        $context['site'] = $this;
        return $context;
    }

    public function add_to_twig($twig) {
        $twig->addFilter(new TwigFilter('myfoo', [$this, 'myfoo']));
        $twig->addFunction(new TwigFunction('home_url', function() {
            return home_url();
        }));
        $twig->addExtension(new StringLoaderExtension());
        $twig->addFunction(new TwigFunction('do_shortcode', 'do_shortcode'));
        return $twig;
    }

    public function setup_front_page_context() {
        if (is_front_page()) {
            $context = Timber::context();
            $context['posts'] = new Timber\PostQuery();
            Timber::render('front-page.twig', $context);
        }
    }
    public function setup_benevoles_page_context() {
        if (is_page('benevoles')) {
            $context = Timber::context();
            // Here you should fetch and assign your data to the context. For example:
            // $context['volunteers'] = get_volunteers_data(); // Replace with your actual data fetching logic
            echo Timber::compile('benevoles-page.twig', $context);
        }
    }
    
    
} 