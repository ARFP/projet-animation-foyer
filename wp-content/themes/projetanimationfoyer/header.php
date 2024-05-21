<?php

namespace App;

use Timber\Timber;

$templates = array('base.twig');

$context = Timber::context([
	'title' => $title,
]);

Timber::render($templates, $context);
