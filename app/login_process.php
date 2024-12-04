<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Rechercher l'utilisateur par email
    $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifier si l'utilisateur existe et si le mot de passe est correct
    if ($user && password_verify($password, $user['Password'])) {
        // Stocker les informations utilisateur dans la session
        $_SESSION['user_id'] = $user['IdUser']; // Corrigé ici
        $_SESSION['user_email'] = $user['email'];
        header('Location: dashbord.php');
        exit();
    } else {
        // Afficher un message d'erreur si les identifiants sont incorrects
        echo "<div class='alert alert-danger text-center'>Email ou mot de passe incorrect.</div>";
    }
}
?>
