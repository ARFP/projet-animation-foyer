<?php

namespace Projet;

use Timber\Timber;

$context = Timber::context();

// Définir les arguments pour récupérer les articles de la catégorie 'animations'
$args = [
    'category_name' => 'animations', // Assurez-vous que c'est le slug exact de la catégorie
    'post_type' => 'post',           // ou votre type de post personnalisé si nécessaire
    'posts_per_page' => -1           // -1 pour tous les articles
];

$context['posts'] = Timber::get_posts($args);

Timber::render('page-animations.twig', $context);
