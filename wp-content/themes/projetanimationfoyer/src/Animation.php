<?php

namespace Projet;
// error_reporting(E_ALL);
// ini_set('display_errors', TRUE);
// ini_set('display_startup_errors', TRUE);
// require_once __DIR__ . '/vendor/autoload.php';
use Timber\Timber;
use Timber\Site;
use Timber\Menu;


class Animation extends Site {
	
	public function __construct() {
	
		parent::__construct('projetanimationfoyer');
	}
	public function add_to_context($context)
	{
		return $context;
	}

	

}

// new Animation();
