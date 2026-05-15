<?php
/**
 * Accueil client (landing). Le catalogue détaillé est sur `?action=catalogue_public`.
 *
 * @var int $total_parc
 * @var int $total_agences
 */
?>
<section class="hero-pattern py-5 position-relative">
    <div class="container position-relative" style="z-index:1">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <p class="text-uppercase small text-white-50 mb-2">Location courte durée</p>
                <h1 class="display-4 mb-3">Roulez libre, sans lourdeur.</h1>
                <p class="lead text-white-75 mb-4">Réservez un véhicule en quelques clics. Parc varié, tarifs clairs, retrait en agence.</p>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="stat-pill"><?= (int) $total_parc ?> véhicule<?= $total_parc > 1 ? 's' : '' ?> au parc</span>
                    <span class="stat-pill"><?= (int) $total_agences ?> agence<?= $total_agences > 1 ? 's' : '' ?></span>
                </div>
                <a href="?action=catalogue_public" class="btn btn-light btn-lg">Voir le catalogue</a>
                <?php if (empty($_SESSION['user_id'])): ?>
                    <a href="?action=inscription" class="btn btn-outline-light btn-lg ms-2">Créer un compte</a>
                <?php endif; ?>
            </div>
            <div class="col-lg-5 d-none d-lg-block text-center">
                <div class="rounded-4 hero-promo-card p-4 text-center text-lg-start">
                    <p class="small text-white-50 text-uppercase mb-2">Offre du moment</p>
                    <p class="h4 mb-0">Réductions de saison sur les citadines et SUV.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="lap-trust-strip py-2">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="lap-trust-item lap-trust-divider">
                    <div class="lap-trust-icon"><i class="bi bi-shield-check" aria-hidden="true"></i></div>
                    <div>
                        <h3>Véhicules contrôlés</h3>
                        <p>Parc entretenu et état vérifié avant chaque départ.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="lap-trust-item lap-trust-divider">
                    <div class="lap-trust-icon"><i class="bi bi-geo-alt" aria-hidden="true"></i></div>
                    <div>
                        <h3>Réseau d’agences</h3>
                        <p>Retrait et retour dans nos points partenaires partout en France.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="lap-trust-item">
                    <div class="lap-trust-icon"><i class="bi bi-headset" aria-hidden="true"></i></div>
                    <div>
                        <h3>Accompagnement</h3>
                        <p>Équipe disponible pour sécuriser votre réservation.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="lap-section-cta border-top">
    <div class="container py-5">
        <div class="row g-4 align-items-center">
            <div class="col-md-8">
                <h2 class="mb-2">Trouvez votre véhicule</h2>
                <p class="text-muted mb-0">Filtres par type, prix et mot-clé — même expérience que sur notre page catalogue dédiée.</p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="?action=catalogue_public" class="btn btn-primary btn-lg">Ouvrir le catalogue</a>
            </div>
        </div>
    </div>
</section>
