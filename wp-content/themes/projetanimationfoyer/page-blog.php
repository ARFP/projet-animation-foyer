<?php
/**
 * Template Name: Page Blog
 */

namespace Projet;

use Timber\Timber;
use Timber\PostQuery;

// Initialiser le contexte Timber
$context = Timber::context();

// Spécifiez les slugs des catégories pour lesquelles vous souhaitez récupérer les articles
$categories = ['news', 'animations', 'activites' , 'handicap']; // Assurez-vous que les slugs sont corrects

// Paramètres de la requête pour récupérer les articles
$args = [
    'post_type' => 'post',                    // Type de post
    'posts_per_page' => -1,                   // -1 signifie "afficher tous les articles"
    'category_name' => implode(',', $categories) // Fusionner les slugs de catégorie pour la requête
];

// Utiliser Timber::get_posts pour récupérer les articles selon les critères définis dans $args
$context['posts'] = Timber::get_posts($args);

// Envoyer le contexte au template Twig
Timber::render('page-blog.twig', $context);
