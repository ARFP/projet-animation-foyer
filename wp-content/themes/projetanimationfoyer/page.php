<?php

namespace App;

use Timber\Timber;

$context = Timber::context();
$post = $context['post'];

Timber::render(array('page-' . $post->post_name . '.twig', 'page.twig'), $context);

