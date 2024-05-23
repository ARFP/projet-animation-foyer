<?php

namespace Projet;

use Timber\Timber;

$context = Timber::context();
$timber_post = Timber::query_post();
$context['post'] = $timber_post;

Timber::render( array( 'posts/single-' . $timber_post->ID . '.twig', 
						'posts/single-' . $timber_post->post_type . '.twig', 
						'posts/single.twig' ), $context );
						

