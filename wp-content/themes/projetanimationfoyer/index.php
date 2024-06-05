<?php

namespace Projet;

use Timber\Timber;

$templates = array('views/index.twig');

if (is_home()) {
	array_unshift( $templates, 'views/front-page.twig', 'view/home.twig' );
}

$context = Timber::context([
	'foo'   => 'bar',
]);

Timber::render($templates, $context);

