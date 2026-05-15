<?php
/**
 * @var array{vehicules: int, agences: int, reservations: int} $stats
 */
$s = $stats;
?>
<div class="admin-accueil-hero bg-dark text-white py-5 mb-0 border-bottom border-secondary">
    <div class="container">
        <span class="badge text-bg-warning text-dark mb-2">Espace réservé — Administrateur</span>
        <h1 class="h2 fw-bold mb-2">Accueil administration</h1>
        <p class="text-white-50 mb-0 col-lg-8">
            Gestion du site LocAuto Pro : parc, agences, utilisateurs et indicateurs. L’accueil et le catalogue « clients » sont séparés de cet espace.
        </p>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 rounded-4">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">Véhicules en base</div>
                    <div class="display-6 fw-bold text-primary"><?= (int) $s['vehicules'] ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 rounded-4">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">Agences</div>
                    <div class="display-6 fw-bold text-info"><?= (int) $s['agences'] ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 rounded-4">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">Réservations</div>
                    <div class="display-6 fw-bold text-success"><?= (int) $s['reservations'] ?></div>
                </div>
            </div>
        </div>
    </div>

    <h2 class="h5 fw-bold mb-3">Accès rapide — gestion</h2>
    <div class="row g-3 mb-5">
        <div class="col-md-6 col-lg-4">
            <a href="?action=admin_dashboard" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 rounded-4 card-hover-lift">
                    <div class="card-body">
                        <i class="bi bi-speedometer2 fs-2 text-primary d-block mb-2"></i>
                        <h3 class="h6 fw-bold text-dark">Tableau de bord</h3>
                        <p class="small text-muted mb-0">KPI, répartitions, dernières réservations.</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-lg-4">
            <a href="?action=vehicule_liste" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 rounded-4 card-hover-lift border-start border-4 border-success">
                    <div class="card-body">
                        <i class="bi bi-truck-front fs-2 text-success d-block mb-2"></i>
                        <h3 class="h6 fw-bold text-dark">Parc véhicules (gestion)</h3>
                        <p class="small text-muted mb-0">Tableau, ajout, suppression — outils admin.</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-lg-4">
            <a href="?action=agence_liste" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 rounded-4 card-hover-lift">
                    <div class="card-body">
                        <i class="bi bi-building fs-2 text-info d-block mb-2"></i>
                        <h3 class="h6 fw-bold text-dark">Agences</h3>
                        <p class="small text-muted mb-0">Liste des points de retrait partenaires.</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-lg-4">
            <a href="?action=admin_liste" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 rounded-4 card-hover-lift">
                    <div class="card-body">
                        <i class="bi bi-people fs-2 text-warning d-block mb-2"></i>
                        <h3 class="h6 fw-bold text-dark">Utilisateurs</h3>
                        <p class="small text-muted mb-0">Comptes, rôles et inscriptions admin.</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="alert alert-light border rounded-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <strong>Aperçu site client</strong>
            <p class="small text-muted mb-0">Consulter le catalogue public et les fiches comme un visiteur.</p>
        </div>
        <a href="?action=catalogue_public" class="btn btn-outline-secondary" target="_blank" rel="noopener">Ouvrir le catalogue public</a>
    </div>
</div>

<style>
.card-hover-lift { transition: transform .15s ease, box-shadow .15s ease; }
.card-hover-lift:hover { transform: translateY(-3px); box-shadow: 0 12px 28px rgba(15,23,42,.12) !important; }
</style>
