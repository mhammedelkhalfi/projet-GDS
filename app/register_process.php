<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $idDep = $_POST['departement']; // ID du département sélectionné

    try {
        // Démarrer une transaction
        $pdo->beginTransaction();

        // Vérifier si l'email existe déjà dans la table utilisateur
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            echo "<div class='alert alert-danger text-center'>Cet email est déjà utilisé. <a href='register.php'>Réessayer</a></div>";
        } else {
            // Insérer d'abord dans la table utilisateur
            $stmtUser = $pdo->prepare("INSERT INTO utilisateur (email, Password) VALUES (?, ?)");
            $stmtUser->execute([$email, $password]);

            // Récupérer l'ID de l'utilisateur inséré
            $idUser = $pdo->lastInsertId();

            // Insérer ensuite dans la table employe
            $stmtEmp = $pdo->prepare("INSERT INTO employe (Nom, Prenom, IdDep, IdUser) VALUES (?, ?, ?, ?)");
            $stmtEmp->execute([$nom, $prenom, $idDep, $idUser]);

            // Confirmer la transaction
            $pdo->commit();
            header("Location: login.php");
        }
    } catch (Exception $e) {
        // Annuler la transaction en cas d'erreur
        $pdo->rollBack();
        echo "<div class='alert alert-danger text-center'>Erreur lors de l'inscription : " . htmlspecialchars($e->getMessage()) . " <a href='register.php'>Réessayer</a></div>";
    }
}
?>
