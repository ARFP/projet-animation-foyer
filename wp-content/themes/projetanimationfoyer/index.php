<?php

use Timber\PostQuery;
use Timber\Timber;

$context = Timber::get_context();
$context['POSTS'] = new PostQuery();

$templates = ['index.twig'];

Timber::render($templates, $context);
