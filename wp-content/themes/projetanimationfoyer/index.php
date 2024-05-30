<?php

namespace Projet;

use Timber\Timber;

$templates = array('templates/posts/index.twig');

if (is_home()) {
	array_unshift( $templates, 'templates/front-page.twig', 'templates/home.twig' );
}

$context = Timber::context([
	'foo'   => 'bar',
]);

Timber::render($templates, $context);

