<?php
require 'db.php';
session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupération des salles pour l'utilisateur connecté
$stmt = $pdo->prepare("SELECT * FROM salle WHERE IdUser = ?");
$stmt->execute([$user_id]);
$salles = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Salles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="bg-light">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-success">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">MonApp</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="dashbord.php">Home</a>
                        </li>
                        <!-- Gestion de Réservation -->
                        <li class="nav-item">
                            <a class="nav-link" href="reservation.php">Gestion de Réservation</a>
                        </li>
                        <!-- Gestion de Salle -->
                        <li class="nav-item">
                            <a class="nav-link" href="salle.php">Gestion de Salle</a>
                        </li>
                        <!-- Gestion de Profil -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Gestion de Profil
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                                <li><a class="dropdown-item" href="settings.php">Paramètres</a></li>
                                <li><a class="dropdown-item" href="logout.php">Déconnexion</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Contenu de la page -->
        <div class="container mt-5">
            <h1 class="mb-4">Vos Salles</h1>
            <a href="createSalle.php" class="btn btn-success mb-3">Ajouter une Salle</a>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Capacité</th>
                        <th>Type de Salle</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($salles as $salle): ?>
                        <tr>
                            <td><?= htmlspecialchars($salle['NumSalle']) ?></td>
                            <td><?= htmlspecialchars($salle['Capacite']) ?></td>
                            <td><?= htmlspecialchars($salle['TypeSalle']) ?></td>
                            <td>
                                <a href="editSalle.php?id=<?= $salle['NumSalle'] ?>" class="btn btn-primary btn-sm">Modifier</a>
                                <a href="deleteSalle.php?id=<?= $salle['NumSalle'] ?>" class="btn btn-danger btn-sm">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
