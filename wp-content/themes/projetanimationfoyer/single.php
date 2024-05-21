<?php

namespace App;

use Timber\Timber;

$context = Timber::context();
$post = $context['post'];

	Timber::render(array('single-' . $post->ID . '.twig', 'single-' . $post->post_type . '.twig', 'single-' . $post->slug . '.twig', 'single.twig'), $context);


