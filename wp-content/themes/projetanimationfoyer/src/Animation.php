<?php

// namespace Projet;
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
require_once __DIR__ . '/vendor/autoload.php';

use Timber\Site;

/**
 * Class Animation
 */
class Animation extends Site {
	
	public function __construct() {
		add_theme_support('post-formats');
		add_theme_support('post-thumbnails');
		add_theme_support('menus');
		add_filter('timber_context', array($this, 'add_to_context'));
		add_filter('get_twig', array($this, 'add_to_twig'));
		add_action('init', array($this, 'register_post_types'));
		add_action('init', array($this, 'register_taxonomies'));
		parent::__construct();
	}
	/*
	function add_to_context($context) {
		$context['menu'] = new Timber\Menu();
		$context['site'] = $this;
		return $context;
	}*/


}

new Animation();
