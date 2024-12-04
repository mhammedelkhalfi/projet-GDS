<?php
require 'db.php';
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupérer les informations utilisateur
try {
    $stmt = $pdo->prepare("
        SELECT u.email, e.Nom, e.Prenom, d.DescDep 
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
    
    // Récupérer le nombre de réservations par département
    $reservationsStmt = $pdo->query("
        SELECT d.DescDep, COUNT(r.IdReservation) AS totalReservations
        FROM departement d
        LEFT JOIN employe e ON d.IdDep = e.IdDep
        LEFT JOIN reservation r ON e.NumEmploye = r.NumEmploye
        GROUP BY d.DescDep
    ");
    $reservationsData = $reservationsStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "Erreur lors de la récupération des données : " . htmlspecialchars($e->getMessage());
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">MonApp</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
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



    <!-- Bienvenue -->
    <div class="container mt-4">
        <h2 class="text-center">Bienvenue, <?php echo htmlspecialchars($userInfo['Nom'] . " " . $userInfo['Prenom']); ?> !</h2>
        <p class="text-center">Vous êtes dans le département : <strong><?php echo htmlspecialchars($userInfo['DescDep']); ?></strong></p>
    </div>

    <!-- Statistiques graphiques -->
    <div class="container mt-5">
        <h3 class="text-center mb-4">Statistiques</h3>
        <div class="row">
            <div class="col-md-12">
                <canvas id="reservationsChart"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script>
        // Préparer les données pour le graphique
        const labels = <?php echo json_encode(array_column($reservationsData, 'DescDep')); ?>;
        const data = <?php echo json_encode(array_column($reservationsData, 'totalReservations')); ?>;

        // Initialiser le graphique
        const ctx = document.getElementById('reservationsChart').getContext('2d');
        const reservationsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Nombre de réservations par département',
                    data: data,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });
    </script>
</body>
</html>
