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
        parent::__construct();
        $this->register_actions();
        $this->register_filters();
    }

    private function register_actions() {
        add_action('init', array($this, 'clear_all_transients'));
        add_action('init', array($this, 'start_session'), 1);
        add_action('wp_logout', array($this, 'end_session'));
        add_action('wp_login', array($this, 'login_redirect'), 10, 2);
        add_action('after_setup_theme', array($this, 'theme_supports'));
        add_action('after_setup_theme', array($this, 'add_custom_roles'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_custom_styles'));
        add_action('init', array($this, 'register_post_types'));
        add_action('init', array($this, 'register_taxonomies'));
        add_action('init', array($this, 'setup_shortcodes'));
        add_action('wp_loaded', array($this, 'setup_front_page_context'));
        add_action('wp_loaded', array($this, 'setup_benevoles_page_context'));
        add_action('admin_menu', array($this, 'add_admin_pages'));
    }

    private function register_filters() {
        add_filter('timber/context', array($this, 'add_to_context'));
        add_filter('timber/twig', array($this, 'add_to_twig'));
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
                if (in_array('benevole', $user->roles)) {
                    wp_redirect(home_url('/benevoles'));
                } elseif (in_array('admin_benevole', $user->roles)) {
                    wp_redirect(home_url('/admin-benevoles'));
                } else {
                    wp_redirect(home_url('/page-secrete'));
                }
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
        $twig->addFunction(new TwigFunction('current_user_can', function($capability) {
            return current_user_can($capability);
        }));
        return $twig;
    }

    public function setup_front_page_context() {
        if (is_front_page()) {
            $context = Timber::context();
            $context['posts'] = Timber::get_posts();
            Timber::render('front-page.twig', $context);
        }
    }

    public function setup_benevoles_page_context() {
        if (is_page('benevoles')) {
            if (current_user_can('access_benevoles_page')) {
                $context = Timber::context();
                $context['message'] = 'Bienvenue sur la page des bénévoles.';
                Timber::render('benevoles-page.twig', $context);
            } else {
                wp_redirect(home_url('/'));
                exit;
            }
        }
    }

    public function add_custom_roles() {
        add_role(
            'benevole',
            __('Bénévole'),
            array(
                'read' => true,
                'access_benevoles_page' => true,
            )
        );

        add_role(
            'admin_benevole',
            __('Admin Bénévole'),
            array(
                'read' => true,
                'access_benevoles_page' => true,
                'manage_benevoles' => true,
            )
        );
    }

    public function add_admin_pages() {
        add_menu_page(
            __('Gestion des Bénévoles'),
            __('Bénévoles'),
            'manage_benevoles',
            'gestion_benevoles',
            array($this, 'render_benevoles_admin_page')
        );
    }

    public function render_benevoles_admin_page() {
        echo '<div class="wrap">';
        echo '<h1>Gestion des Bénévoles</h1>';
        echo '<p>Cette page vous permet de gérer les bénévoles.</p>';
        // Ajoutez ici le code HTML pour afficher et gérer les bénévoles
        echo '</div>';
    }

    public function setup_shortcodes() {
        add_shortcode('custom_login_form', array($this, 'render_login_form'));
    }

    public function render_login_form() {
        ob_start();
        if (is_user_logged_in()) {
            wp_redirect(home_url());
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = sanitize_text_field($_POST['username']);
            $password = sanitize_text_field($_POST['password']);

            $creds = array(
                'user_login'    => $username,
                'user_password' => $password,
                'remember'      => true
            );

            $user = wp_signon($creds, false);

            if (is_wp_error($user)) {
                $error_message = $user->get_error_message();
            } else {
                wp_redirect(home_url());
                exit;
            }
        }
        ?>

        <div class="login-form">
            <h2>Login</h2>
            <?php if (!empty($error_message)): ?>
                <div class="error">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            <form method="post" action="">
                <p>
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" required>
                </p>
                <p>
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required>
                </p>
                <p>
                    <input type="submit" value="Login">
                </p>
            </form>
        </div>

        <?php
        return ob_get_clean();
    }
}
