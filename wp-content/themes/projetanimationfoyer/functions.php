<?php 

namespace Projet;
use Timber\Timber;

// Load Composer dependencies.
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/Animation.php';

// Initialize Timber.
Timber::init();


// functions.php

// Vérification si le fichier est inclus directement
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Ajout d'une erreur intentionnelle pour vérifier le fonctionnement du débogage
error_log('Test erreur de débogage - ceci est un test.');

// Vos autres fonctions et hooks ici...

// Exemple d'une fonction existante
function mon_theme_enqueue_styles() {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
}
add_action('wp_enqueue_scripts', 'mon_theme_enqueue_styles');


//Instancier la classe Animation
new Animation();



