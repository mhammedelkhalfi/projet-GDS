<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$id = $_GET['id'];

// Vérification de la salle appartenant à l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM salle WHERE NumSalle = ? AND IdUser = ?");
$stmt->execute([$id, $user_id]);
$salle = $stmt->fetch();

if (!$salle) {
    echo "Erreur : Salle introuvable ou accès non autorisé.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $capacite = $_POST['capacite'];
    $typeSalle = $_POST['type_salle'];

    $stmt = $pdo->prepare("UPDATE salle SET Capacite = ?, TypeSalle = ? WHERE NumSalle = ? AND IdUser = ?");
    $stmt->execute([$capacite, $typeSalle, $id, $user_id]);

    header('Location: salle.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une Salle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Modifier la Salle</h1>
        <form method="POST">
            <div class="mb-3">
                <label for="capacite" class="form-label">Capacité</label>
                <input type="number" class="form-control" id="capacite" name="capacite" value="<?= htmlspecialchars($salle['Capacite']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="type_salle" class="form-label">Type de Salle</label>
                <select class="form-control" id="type_salle" name="type_salle" required>
                    <option <?= $salle['TypeSalle'] === 'Salle de conférence' ? 'selected' : '' ?>>Salle de conférence</option>
                    <option <?= $salle['TypeSalle'] === 'Bureau' ? 'selected' : '' ?>>Bureau</option>
                    <option <?= $salle['TypeSalle'] === 'Salle de formation' ? 'selected' : '' ?>>Salle de formation</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="salle.php" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
</body>
</html>