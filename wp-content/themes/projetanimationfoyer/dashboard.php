<?php
// Vérifier que l'utilisateur est connecté et récupérer ses données
session_start();
if (!isset($_SESSION['user_role'])) {
    header('Location: login.php'); // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    exit;
}

// Accès restreint par rôle
if ($_SESSION['user_role'] != 'admin') {
    die('Accès refusé'); // Terminer l'exécution pour les utilisateurs non administrateurs
}

// Code pour les administrateurs ici
echo 'Bienvenue sur la page admin !';
