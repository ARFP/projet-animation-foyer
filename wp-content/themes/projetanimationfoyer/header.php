<?php

namespace Projet;

use Timber\Timber;

// Charger le contexte Timber
$context = Timber::context();

// Construire l'URL de l'image
$context['logo_url'] = get_stylesheet_directory_uri() . '/assets/img/wp-content/themes/projetanimationfoyer/assets/img/CRMlogo1.jpg';

// Rendre le template Twig
Timber::render('header.twig', $context);

