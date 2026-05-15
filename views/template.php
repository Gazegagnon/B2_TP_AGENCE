<?php
/** @var string $content Contenu HTML de la vue, défini dans AbstractController::render() avant inclusion de ce fichier */
/** @var string $pageTitle */
/** @var list<array{type: string, message: string}> $flashMessages */
$lapSupport = [
    'email' => 'service.client@locautopro.fr',
    'phone_display' => '09 70 35 12 34',
    'phone_href' => '+33970351234',
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'LocAuto Pro', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/app.css">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body class="lap-body">
<?php
$navRole = $_SESSION['role'] ?? '';
$navIsAdmin = $navRole === 'ADMIN';
$navIsCommercial = $navRole === 'COMMERCIAL';
?>

<nav class="lap-navbar navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="?action=home" aria-label="LocAuto Pro — accueil">LocAuto<span class="lap-brand-accent"> Pro</span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain" aria-controls="navMain" aria-expanded="false" aria-label="Menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="?action=home"><?= $navIsAdmin ? 'Accueil admin' : 'Accueil' ?></a>
                </li>
                <?php if ($navIsAdmin): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="?action=catalogue_public" target="_blank" rel="noopener">Vitrine</a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="?action=admin_dashboard"><i class="bi bi-speedometer2 me-1 d-none d-sm-inline"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="?action=admin_liste">Utilisateurs</a></li>
                    <li class="nav-item"><a class="nav-link" href="?action=agence_liste">Agences</a></li>
                    <li class="nav-item"><a class="nav-link" href="?action=vehicule_liste">Parc (gestion)</a></li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="?action=catalogue_public">Catalogue</a>
                    </li>
                    <?php if (!empty($_SESSION['user_id']) && !$navIsAdmin): ?>
                        <li class="nav-item"><a class="nav-link" href="?action=client_messagerie">Aide &amp; contact</a></li>
                    <?php endif; ?>
                    <?php if ($navIsCommercial): ?>
                        <li class="nav-item"><a class="nav-link" href="?action=commercial_dashboard">Espace pro</a></li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <?php if (!empty($_SESSION['user_id'])): ?>
                    <span class="text-white-50 small d-none d-md-inline"><?= htmlspecialchars((string) $navRole, ENT_QUOTES, 'UTF-8') ?></span>
                    <a class="btn btn-outline-light btn-sm" href="?action=compte"><?= $navIsAdmin ? 'Mon espace admin' : 'Mon compte' ?></a>
                    <a class="btn btn-danger btn-sm" href="?action=deconnexion">Déconnexion</a>
                <?php else: ?>
                    <a class="btn btn-outline-light btn-sm" href="?action=connexion">Connexion</a>
                    <a class="btn btn-primary btn-sm" href="?action=inscription">Inscription</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<?php if (!empty($flashMessages)): ?>
<div class="container lap-flash-wrap mt-3">
    <?php foreach ($flashMessages as $f): ?>
        <?php
        $type = $f['type'] ?? 'info';
        if (!in_array($type, ['success', 'danger', 'warning', 'info', 'primary'], true)) {
            $type = 'info';
        }
        ?>
        <div class="alert alert-<?= htmlspecialchars($type, ENT_QUOTES, 'UTF-8') ?> alert-dismissible fade show" role="alert" data-auto-dismiss="7000">
            <?= htmlspecialchars($f['message'] ?? '', ENT_QUOTES, 'UTF-8') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<main class="flex-grow-1">
    <?= $content; ?>
</main>

<footer class="lap-footer text-white">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <h6 class="text-uppercase text-white-50 small">LocAuto Pro</h6>
                <p class="small text-white-50 mb-0">Location de véhicules : voitures, motos et utilitaires. Réservez en ligne, retirez en agence partenaire.</p>
            </div>
            <div class="col-md-4">
                <h6 class="text-uppercase text-white-50 small">Liens</h6>
                <ul class="list-unstyled small mb-0">
                    <li><a href="?action=home"><?= $navIsAdmin ? 'Accueil admin' : 'Accueil' ?></a></li>
                    <li><a href="?action=catalogue_public"><?= $navIsAdmin ? 'Catalogue public (aperçu)' : 'Catalogue' ?></a></li>
                    <?php if (empty($_SESSION['user_id'])): ?>
                        <li><a href="?action=connexion">Connexion</a></li>
                    <?php else: ?>
                        <li><a href="?action=compte"><?= $navIsAdmin ? 'Espace administrateur' : 'Mon compte' ?></a></li>
                        <?php if (!$navIsAdmin): ?>
                            <li><a href="?action=client_messagerie">Service client</a></li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="col-md-4">
                <h6 class="text-uppercase text-white-50 small">Contact</h6>
                <ul class="list-unstyled small text-white-50 mb-0">
                    <li class="mb-1"><a href="?action=client_messagerie">Messagerie</a> <span class="text-white-50">(connectés)</span></li>
                    <li class="mb-1"><a href="mailto:<?= htmlspecialchars($lapSupport['email'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($lapSupport['email'], ENT_QUOTES, 'UTF-8') ?></a></li>
                    <li><a href="tel:<?= htmlspecialchars($lapSupport['phone_href'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($lapSupport['phone_display'], ENT_QUOTES, 'UTF-8') ?></a></li>
                </ul>
            </div>
        </div>
        <hr class="border-secondary my-4">
        <p class="small text-white-50 text-center mb-0">&copy; <?= date('Y') ?> LocAuto Pro — Projet pédagogique location de véhicules</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="assets/js/app.js" defer></script>
</body>
</html>
