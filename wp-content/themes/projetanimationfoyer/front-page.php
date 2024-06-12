<?php

namespace Projet;

use Timber\Timber;

$context = Timber::context();


// Récupérer les catégories en excluant la catégorie 'benevoles'
$categories = get_categories([
    'exclude' => get_cat_ID('benevoles') // Remplacez 'benevoles' par le slug ou le nom exact de la catégorie
]);

// Récupérer les derniers articles de chaque catégorie
$latest_posts_by_category = array();
foreach ($categories as $category) {
    $args = array(
        'posts_per_page' => 1,
        'category_name' => $category->slug, 
        'orderby' => 'date',
        'order' => 'DESC',
    );
    $latest_posts = Timber::get_posts($args);
    if (!empty($latest_posts)) {
        $latest_posts_by_category[$category->slug] = $latest_posts[0];
    }
}

$context['latest_posts_by_category'] = $latest_posts_by_category;

Timber::render(array('front-page.twig'), $context);

// var_dump($latest_posts_by_category);
