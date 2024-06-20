<?php
// Supposons que vous avez une connexion à une base de données établie ici

// Connexion de l'utilisateur
$username = $_POST['username'];  // Récupéré du formulaire de connexion
$password = $_POST['password'];  // Récupéré du formulaire de connexion

// Récupération du mot de passe hashé depuis la base de données
$query = $pdo->prepare("SELECT password FROM users WHERE username = ?");
$query->execute([$username]);
$hashed_password = $query->fetchColumn();

if (password_verify($password, $hashed_password)) {
    echo 'Connexion réussie !';
    // Éventuellement, rediriger vers une page d'accueil ou de dashboard
} else {
    echo 'Mot de passe incorrect !';
    // Éventuellement, rediriger vers la page de connexion avec un message d'erreur
}
