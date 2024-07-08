<?php 

namespace Projet;
use Timber\Timber;

// Load Composer dependencies.
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/Animation.php';

// Initialize Timber.
Timber::init();

//Instancier la classe Animation
new Animation();



