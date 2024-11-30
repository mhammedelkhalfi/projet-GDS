<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $departement = $_POST['departement'];

    try {
        // Début d'une transaction
        $pdo->beginTransaction();

        // Étape 1 : Vérifier si l'email existe déjà
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            echo "<div class='alert alert-danger text-center'>Cet email est déjà utilisé. <a href='register.php'>Réessayer</a></div>";
        } else {
            // Étape 2 : Insérer dans le département
            $stmtDep = $pdo->prepare("INSERT INTO departement (DescDep) VALUES (?)");
            $stmtDep->execute([$departement]);
            $idDep = $pdo->lastInsertId(); // Récupérer l'ID du département inséré

            // Étape 3 : Insérer dans la table employé
            $stmtEmp = $pdo->prepare("INSERT INTO employe (Nom, Prenom, IdDep) VALUES (?, ?, ?)");
            $stmtEmp->execute([$nom, $prenom, $idDep]);
            $idEmp = $pdo->lastInsertId(); // Récupérer l'ID de l'employé

            // Étape 4 : Insérer dans la table utilisateur
            $stmtUser = $pdo->prepare("INSERT INTO utilisateur (email, Password) VALUES (?, ?)");
            $stmtUser->execute([$email, $password]);

            // Validation de la transaction
            $pdo->commit();
            echo "<div class='alert alert-success text-center'>Inscription réussie. <a href='login.php'>Connectez-vous</a></div>";
        }
    } catch (Exception $e) {
        // Annuler la transaction en cas d'erreur
        $pdo->rollBack();
        echo "<div class='alert alert-danger text-center'>Erreur lors de l'inscription : " . htmlspecialchars($e->getMessage()) . " <a href='register.php'>Réessayer</a></div>";
    }
}
?>
