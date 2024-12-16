<?php

namespace Projet;

use Timber\Timber;

$context = Timber::context();
$post = $context['post'];

Timber::render(array('page-' . $post->post_name . '.twig', 'page-contact.twig'), $context);

if (function_exists('wp_nonce_field')) {
    $context['nonce_field'] = wp_nonce_field('contact_form', '_wpnonce', true, false);
}
