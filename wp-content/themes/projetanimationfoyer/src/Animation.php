<?php

namespace Projet;
// error_reporting(E_ALL);
// ini_set('display_errors', TRUE);
// ini_set('display_startup_errors', TRUE);
// require_once __DIR__ . '/vendor/autoload.php';
use Timber\Timber;
use Timber\Site;


class Animation extends Site {
	
	public function __construct() {
		// add_theme_support('post-formats');
		// add_theme_support('post-thumbnails');
		// add_theme_support('menus');
		// add_filter('timber_context', array($this, 'add_to_context'));
		// add_filter('get_twig', array($this, 'add_to_twig'));
		// add_action('init', array($this, 'register_post_types'));
		// add_action('init', array($this, 'register_taxonomies'));
		parent::__construct('projetanimationfoyer');
	}
	public function add_to_context($context)
	{
		$context['foo']   = 'bar';
		$context['stuff'] = 'I am a value set in your functions.php file';
		$context['notes'] = 'These values are available everytime you call Timber::context();';
		$context['menu']  = Timber::get_menu();
		$context['site']  = $this;

		return $context;
	}
	/*
	function add_to_context($context) {
		$context['menu'] = new Timber\Menu();
		$context['site'] = $this;
		return $context;
	}*/


}

// new Animation();
