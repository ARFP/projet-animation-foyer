<?php

namespace Projet;

use fantassin\core\WordPress\HasHooks;
use Timber\Theme as TimberTheme;

class Animation extends TimberTheme implements HasHooks{
	
	public function __construct() {
		parent::__construct( slug: 'projetanimationfoyer' );
	}
	public function hooks() {
		add_filter('timber/context', [$this, 'add_to_context']);
}
	public function add_to_context($context) {
		var_dump($context);
	}
}