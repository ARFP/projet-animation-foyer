<?php

namespace Projet;

use Timber\Timber;

// Charger le contexte Timber
$context = Timber::context();

// Construire l'URL de l'image
$context['logo_url'] = get_stylesheet_directory_uri() . '/assets/img/logo-centre-de-readaptation-de-mulhouse-colors.svg';

// Rendre le template Twig
Timber::render('header.twig', $context);

