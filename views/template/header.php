<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Location de véhicules</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<header class="bg-light p-3 mb-4">
    <nav class="container d-flex justify-content-between">

        <div>
            <a href="?action=home" class="btn btn-outline-success">Home</a>
            <?php if(isset($_SESSION['user_id']) && $_SESSION['role'] === "ADMIN"): ?>
                <a href="?action=admin_liste" class="btn btn-success">Users</a>
                <a href="?actionAgence=agence" class="btn btn-success">Agence</a>
                <a href="?actionVehicule=vehicule" class="btn btn-success">Véhicule</a>
            <?php endif; ?>
        </div>

        <div>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a class="btn btn-primary" href="?actionUser=compte&id=<?= $_SESSION['user_id'] ?>">Mon compte</a>
                <a class="btn btn-danger" href="?actionUser=deconnexion">Déconnexion</a>
            <?php else: ?>
                <a class="btn btn-success" href="?actionUser=inscription">Inscription</a>
                <a class="btn btn-success" href="?actionUser=connexion">Connexion</a>
            <?php endif; ?>
        </div>

    </nav>