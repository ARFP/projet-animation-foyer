<?php

use Timber\Timber;
use Timber\PostQuery;

// Créer une nouvelle instance de WP_Query avec les arguments nécessaires
$args = array(
    'post_type' => 'post', // Type de post, par exemple 'post', 'page', ou un type de post personnalisé
    'posts_per_page' => 10 // Nombre de posts à récupérer
);
$wp_query = new WP_Query($args);

$context = Timber::context(); // Utilisation de Timber::context()

// Passer l'objet WP_Query à Timber\PostQuery
$context['posts'] = new PostQuery($wp_query);

$templates = array('index.twig'); // Assurez-vous que le chemin vers votre fichier Twig est correct

Timber::render($templates, $context);
