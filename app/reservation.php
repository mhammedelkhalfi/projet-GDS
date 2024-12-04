<?php
// Inclusion de la connexion à la base de données
include('connexion.php');

// Récupérer les réservations avec les informations de salle, capacité et durée
$reservations = $pdo->query("SELECT r.*, s.Capacite, s.TypeSalle FROM reservation r JOIN salle s ON r.NumSalle = s.NumSalle")->fetchAll(PDO::FETCH_ASSOC);

// Fonction pour calculer l'heure de fin
function calculerHeureFin($heureDebut, $duree) {
    // Convertir l'heure de début en timestamp
    $timestampDebut = strtotime($heureDebut);
    
    // Ajouter la durée (en heures) à l'heure de début
    $timestampFin = strtotime("+$duree hours", $timestampDebut);
    
    // Retourner l'heure de fin au format "HH:MM"
    return date("H:i", $timestampFin);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Réservations</title>
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
    <div class="container mt-4">
    <h2 class="text-center">Gestion des Réservations</h2>

<!-- Bouton pour afficher le formulaire d'ajout -->
<a href="createReservation.php" class="btn btn-primary mb-3">Ajouter une réservation</a>

<!-- Table des réservations -->
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Date</th>
            <th>Heure Début</th>
            <th>Durée</th>
            <th>Heure Fin</th> <!-- Ajout de la colonne Heure Fin -->
            <th>Capacité</th>
            <th>Numéro Salle</th>
            <th>Type de Salle</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($reservations as $reservation): ?>
            <tr>
                <td><?= $reservation['IdReservation'] ?></td>
                <td><?= $reservation['DateReservation'] ?></td>
                <td><?= $reservation['HeureDebut'] ?></td>
                <td><?= $reservation['Duree'] ?> heures</td> <!-- Affichage de la durée -->
                <td><?= calculerHeureFin($reservation['HeureDebut'], $reservation['Duree']) ?></td> <!-- Calcul de l'heure de fin -->
                <td><?= $reservation['Capacite'] ?></td>
                <td><?= $reservation['NumSalle'] ?></td>
                <td><?= $reservation['TypeSalle'] ?></td>
                <td>
                    <a href="createReservation.php?id=<?= $reservation['IdReservation'] ?>" class="btn btn-warning btn-sm">Modifier</a>
                    <a href="deleteReservation.php?id=<?= $reservation['IdReservation'] ?>" class="btn btn-danger btn-sm">Supprimer</a>
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