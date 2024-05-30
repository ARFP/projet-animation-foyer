<?php

namespace Projet;
use Timber\Timber;

$context = Timber::context();
$post = $context['post'];

// Récupérer les catégories
$categories = get_categories();

// Récupérer les derniers articles de chaque catégorie
$latest_posts_by_category = array();
foreach ($categories as $category) {
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => 1,
        'cat' => $category->term_id,
        'orderby' => 'date',
        'order' => 'DESC',
    );
    $query = new \WP_Query($args);
    if ($query->have_posts()) {
        $latest_posts_by_category[$category->slug] = $query->posts[0];
    }
    wp_reset_postdata(); // Réinitialiser la requête
}

// Afficher le contenu de $latest_posts_by_category pour vérification
var_dump($latest_posts_by_category);

$context['latest_posts_by_category'] = $latest_posts_by_category;

Timber::render(array('page-' . $post->post_name . '.twig', 'front-page.twig'), $context);
