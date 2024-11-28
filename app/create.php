<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $capacite = $_POST['capacite'];
    $typeSalle = $_POST['type_salle'];

    $stmt = $pdo->prepare("INSERT INTO salle (Capacite, TypeSalle) VALUES (?, ?)");
    $stmt->execute([$capacite, $typeSalle]);

    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Salle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Ajouter une Salle</h1>
        <form method="POST">
            <div class="mb-3">
                <label for="capacite" class="form-label">Capacité</label>
                <input type="number" class="form-control" id="capacite" name="capacite" required>
            </div>
            <div class="mb-3">
                <label for="type_salle" class="form-label">Type de Salle</label>
                <select class="form-control" id="type_salle" name="type_salle" required>
                    <option>Salle de conférence</option>
                    <option>Bureau</option>
                    <option>Salle de formation</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="index.php" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
</body>
</html>
