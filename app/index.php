<?php
require 'db.php';

$stmt = $pdo->query("SELECT * FROM salle");
$salles = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Salles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Gestion des Salles</h1>
        <a href="create.php" class="btn btn-success mb-3">Ajouter une Salle</a>
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
                            <a href="edit.php?id=<?= $salle['NumSalle'] ?>" class="btn btn-primary btn-sm">Modifier</a>
                            <a href="delete.php?id=<?= $salle['NumSalle'] ?>" class="btn btn-danger btn-sm">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
