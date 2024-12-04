<?php
require 'db.php';
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupérer toutes les informations de l'utilisateur
try {
    $stmt = $pdo->prepare("
        SELECT u.email, e.Nom, e.Prenom, e.NumEmploye, e.IdDep, d.DescDep, u.Password AS user_password
        FROM utilisateur u
        LEFT JOIN employe e ON u.IdUser = e.IdUser
        LEFT JOIN departement d ON e.IdDep = d.IdDep
        WHERE u.IdUser = ?
    ");
    $stmt->execute([$user_id]);
    $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$userInfo) {
        echo "Erreur : Informations utilisateur non trouvées.";
        exit();
    }

    // Ajouter une validation pour les clés manquantes
    if (!isset($userInfo['user_password'])) {
        $userInfo['user_password'] = null; // Définir une valeur par défaut
    }
} catch (Exception $e) {
    echo "Erreur lors de la récupération des données : " . htmlspecialchars($e->getMessage());
    exit();
}

// Si le formulaire de changement de mot de passe est soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $oldPassword = $_POST['old_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Vérifier si l'ancien mot de passe est correct
    if ($userInfo['user_password'] && password_verify($oldPassword, $userInfo['user_password'])) {
        if ($newPassword === $confirmPassword) {
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

            try {
                // Mettre à jour le mot de passe
                $updatePasswordStmt = $pdo->prepare("UPDATE utilisateur SET Password = ? WHERE IdUser = ?");
                $updatePasswordStmt->execute([$hashedPassword, $user_id]);
                $passwordMessage = "Mot de passe mis à jour avec succès!";
            } catch (Exception $e) {
                $passwordMessage = "Erreur lors de la mise à jour du mot de passe : " . htmlspecialchars($e->getMessage());
            }
        } else {
            $passwordMessage = "Le nouveau mot de passe et la confirmation ne correspondent pas.";
        }
    } else {
        $passwordMessage = "L'ancien mot de passe est incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres de votre compte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Paramètres de votre compte</h1>

        <!-- Message de confirmation pour les informations -->
        <?php if (isset($message)): ?>
            <div class="alert alert-success">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Message de confirmation pour le mot de passe -->
        <?php if (isset($passwordMessage)): ?>
            <div class="alert alert-warning">
                <?php echo $passwordMessage; ?>
            </div>
        <?php endif; ?>

        <!-- Boutons pour ouvrir les modals -->
        <div class="d-flex gap-3">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#updateInfoModal">Mettre à jour les informations</button>
            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#changePasswordModal">Changer le mot de passe</button>
        </div>

        <hr class="my-4">

        <a href="dashbord.php" class="btn btn-secondary">Retour au tableau de bord</a>
    </div>

    <!-- Modal de mise à jour des informations utilisateur -->
    <div class="modal fade" id="updateInfoModal" tabindex="-1" aria-labelledby="updateInfoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateInfoModalLabel">Mettre à jour les informations</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($userInfo['email']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="Nom" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="Nom" name="Nom" value="<?= htmlspecialchars($userInfo['Nom']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="Prenom" class="form-label">Prénom</label>
                            <input type="text" class="form-control" id="Prenom" name="Prenom" value="<?= htmlspecialchars($userInfo['Prenom']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="DescDep" class="form-label">Département</label>
                            <input type="text" class="form-control" id="DescDep" name="DescDep" value="<?= htmlspecialchars($userInfo['DescDep']) ?>" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" name="update_info" class="btn btn-primary">Mettre à jour</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de changement de mot de passe -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">Changer le mot de passe</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="old_password" class="form-label">Ancien mot de passe</label>
                            <input type="password" class="form-control" id="old_password" name="old_password" required>
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label">Nouveau mot de passe</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                        </div>

                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirmer le nouveau mot de passe</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" name="change_password" class="btn btn-danger">Changer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
