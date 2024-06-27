<?php

namespace Projet;

use Timber\Timber;

$context = Timber::context();
$post = $context['post'];


Timber::render(array('page-' . $post->post_name . '.twig', 'page-secrete.twig'), $context);
