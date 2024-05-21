<?php

namespace Projet;
use Timber\Theme as TimberTheme;
use fantassin\core\WordPress\HasHooks;
class Animation extends TimberTheme implements HasHooks{
	
	public function __construct() {
		parent::__construct( slug: 'projetanimationfoyer' );
	}
	public function hooks() {
	// TODO:implements hooks() methods.
}
}