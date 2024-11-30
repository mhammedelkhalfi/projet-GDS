<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Vérification des informations d'identification
    $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['Password'])) {
        echo "<div class='alert alert-success text-center'>Connexion réussie. Bienvenue, " . htmlspecialchars($user['email']) . "! <a href='home.php'>Aller à l'accueil</a></div>";
    } else {
        echo "<div class='alert alert-danger text-center'>Email ou mot de passe incorrect. <a href='login.php'>Réessayer</a></div>";
    }
}
?>
