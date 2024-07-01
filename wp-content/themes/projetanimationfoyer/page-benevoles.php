<?php

namespace Projet;

use Timber\Timber;

$context = Timber::context();
$post = $context['post'];

/*
Template Name: Benevoles Page
*/

if (!is_user_logged_in() || !current_user_can('access_benevoles_page')) {
    wp_redirect(home_url('/login'));
    exit;
}

get_header();
?>

<div class="benevoles-page">
    <h2>Page des Bénévoles</h2>
    <p>Bienvenue sur la page des bénévoles.</p>
    <div>
        Voici des informations confidentielles ou des fonctionnalités accessibles uniquement aux bénévoles.
    </div>
</div>

<?php get_footer(); ?>


Timber::render(array('page-' . $post->post_name . '.twig', 'page-benevoles.twig'), $context);
