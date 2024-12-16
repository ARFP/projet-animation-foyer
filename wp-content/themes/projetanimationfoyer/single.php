<?php

namespace Projet;

use Timber\Timber;

$context = Timber::context();
$post = $context['post'];
$templates = array('templates/single-' . $post->post_type . '.twig', 'posts/single-post.twig');

// if (post_password_required($post->ID)) {
// 	$templates = 'templates/single-password.twig';
// } 

Timber::render($templates, $context);
