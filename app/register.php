<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enregistrement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header text-center bg-primary text-white">
                        <h3>Créer un compte</h3>
                    </div>
                    <div class="card-body">
                        <form action="register_process.php" method="POST">
                            <!-- Informations utilisateur -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Email :</label>
                                <input type="email" class="form-control" name="email" id="email" placeholder="Entrez votre email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Mot de passe :</label>
                                <input type="password" class="form-control" name="password" id="password" placeholder="Entrez votre mot de passe" required>
                            </div>

                            <!-- Informations employé -->
                            <h5 class="text-primary">Informations employé</h5>
                            <div class="mb-3">
                                <label for="nom" class="form-label">Nom :</label>
                                <input type="text" class="form-control" name="nom" id="nom" placeholder="Entrez votre nom" required>
                            </div>
                            <div class="mb-3">
                                <label for="prenom" class="form-label">Prénom :</label>
                                <input type="text" class="form-control" name="prenom" id="prenom" placeholder="Entrez votre prénom" required>
                            </div>

                            <!-- Liste déroulante des départements -->
                            <h5 class="text-primary">Informations département</h5>
                            <div class="mb-3">
                                <label for="departement" class="form-label">Nom du département :</label>
                                <select class="form-control" name="departement" id="departement" required>
                                    <option value="">-- Sélectionnez un département --</option>
                                    <?php
                                    require 'db.php';
                                    try {
                                        $stmt = $pdo->query("SELECT IdDep, DescDep FROM departement");
                                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            echo "<option value='{$row['IdDep']}'>{$row['DescDep']}</option>";
                                        }
                                    } catch (Exception $e) {
                                        echo "<option disabled>Erreur lors du chargement des départements</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">S'inscrire</button>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <p>Déjà un compte ? <a href="login.php" class="text-primary">Se connecter</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
