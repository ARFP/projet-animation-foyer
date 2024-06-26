<?php
/**
 * Template Name: Page Secrète
 */

$context = Timber::context();

// Ajoutez des données spécifiques que vous souhaitez transmettre au template Twig
$context['message'] = "Bienvenue sur la page secrète accessible uniquement aux utilisateurs authentifiés.";

// Charger des informations spécifiques si nécessaire
// Par exemple, charger des posts, des utilisateurs, des données spécifiques...
// $context['data'] = new Timber\PostQuery();

// Rendre le template avec le contexte
Timber::render('page-secrete.twig', $context);
