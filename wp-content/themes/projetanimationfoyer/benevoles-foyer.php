/**
 * Template Name: Page Secrète
 */

$context = Timber::context();

$context['message'] = "Bienvenue sur la page secrète accessible uniquement aux utilisateurs authentifiés.";

// Charger des informations spécifiques si nécessaire
// $context['data'] = new Timber\PostQuery();

Timber::render('page-secrete.twig', $context);
