<?php
// Inclusion de la connexion à la base de données
include('connexion.php');

// Initialisation des variables
$id = $date_reservation = $heure_debut = $duree = $capacite = $num_salle = $type_salle = "";
$error_message = "";
$types_salle_disponibles = [];

// Fonction pour calculer l'heure de fin
function calculerHeureFin($heureDebut, $duree) {
    $timestampDebut = strtotime($heureDebut);
    $timestampFin = strtotime("+$duree hours", $timestampDebut);
    return date("H:i", $timestampFin);
}

// Si une réservation doit être modifiée
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM reservation WHERE IdReservation = :id");
    $stmt->execute(['id' => $id]);
    $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($reservation) {
        $date_reservation = $reservation['DateReservation'];
        $heure_debut = $reservation['HeureDebut'];
        $duree = $reservation['Duree'];
        $capacite = $reservation['Capacite'];
        $num_salle = $reservation['NumSalle'];
    }
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $date_reservation = $_POST['date_reservation'] ?? "";
    $heure_debut = $_POST['heure_debut'] ?? "";
    $duree = $_POST['duree'] ?? 0;
    $capacite = $_POST['capacite'] ?? 0;
    $type_salle = $_POST['type_salle'] ?? "";

    // Calcul de l'heure de fin
    $heure_fin = calculerHeureFin($heure_debut, $duree);

    // Vérification des conflits
    $stmt = $pdo->prepare(
        "SELECT * FROM reservation WHERE DateReservation = :date AND NumSalle = :salle AND IdReservation != :id"
    );
    $stmt->execute(['date' => $date_reservation, 'salle' => $type_salle, 'id' => $id]);
    $reservations_existantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $chevauchement = false;
    foreach ($reservations_existantes as $reservation_existante) {
        $heure_fin_existante = calculerHeureFin($reservation_existante['HeureDebut'], $reservation_existante['Duree']);
        if (($heure_debut >= $reservation_existante['HeureDebut'] && $heure_debut < $heure_fin_existante) ||
            ($heure_fin > $reservation_existante['HeureDebut'] && $heure_fin <= $heure_fin_existante)) {
            $chevauchement = true;
            break;
        }
    }

    if ($chevauchement) {
        $error_message = "Conflit : la réservation chevauche une autre réservation.";
    } else {
        // Ajouter ou modifier la réservation
        if ($id) {
            $stmt = $pdo->prepare(
                "UPDATE reservation 
                SET DateReservation = :date, HeureDebut = :heure_debut, Duree = :duree, Capacite = :capacite, NumSalle = :salle 
                WHERE IdReservation = :id"
            );
            $stmt->execute([
                'date' => $date_reservation,
                'heure_debut' => $heure_debut,
                'duree' => $duree,
                'capacite' => $capacite,
                'salle' => $type_salle,
                'id' => $id
            ]);
        } else {
            $stmt = $pdo->prepare(
                "INSERT INTO reservation (DateReservation, HeureDebut, Duree, Capacite, NumSalle) 
                VALUES (:date, :heure_debut, :duree, :capacite, :salle)"
            );
            $stmt->execute([
                'date' => $date_reservation,
                'heure_debut' => $heure_debut,
                'duree' => $duree,
                'capacite' => $capacite,
                'salle' => $type_salle
            ]);
        }
        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $id ? "Modifier" : "Ajouter" ?> une réservation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="container my-4">
    <h2 class="text-center"><?= $id ? "Modifier" : "Ajouter" ?> une réservation</h2>

    <?php if ($error_message): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="date_reservation" class="form-label">Date de réservation</label>
            <input type="date" class="form-control" id="date_reservation" name="date_reservation" value="<?= htmlspecialchars($date_reservation) ?>" min="<?= date('Y-m-d') ?>" required>
        </div>
        <div class="mb-3">
            <label for="heure_debut" class="form-label">Heure de début</label>
            <input type="time" class="form-control" id="heure_debut" name="heure_debut" value="<?= htmlspecialchars($heure_debut) ?>" required>
        </div>
        <div class="mb-3">
            <label for="duree" class="form-label">Durée (en heures)</label>
            <input type="number" class="form-control" id="duree" name="duree" value="<?= htmlspecialchars($duree) ?>" required>
        </div>
        <div class="mb-3">
            <label for="capacite" class="form-label">Capacité</label>
            <input type="number" class="form-control" id="capacite" name="capacite" value="<?= htmlspecialchars($capacite) ?>" required>
        </div>
        <div class="mb-3">
            <label for="type_salle" class="form-label">Type de salle</label>
            <select class="form-control" id="type_salle" name="type_salle" required>
                <option value="">-- Sélectionner --</option>
                <?php foreach ($types_salle_disponibles as $salle): ?>
                    <option value="<?= htmlspecialchars($salle['NumSalle']) ?>" <?= $salle['NumSalle'] == $type_salle ? 'selected' : '' ?>>
                        <?= htmlspecialchars($salle['TypeSalle']) ?> (Capacité : <?= htmlspecialchars($salle['Capacite']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary"><?= $id ? "Modifier" : "Ajouter" ?></button>
        <a href="index.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>

<script>
$(document).ready(function() {
    $('#capacite').on('change', function() {
        let capacite = $(this).val();
        if (capacite) {
            $.get('get_salles.php', { capacite: capacite }, function(data) {
                $('#type_salle').html(data);
            });
        }
    });
});
</script>
</body>
</html>
