<?php
/** @var User $user */
?>
<div class="container py-4">
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-4 border-warning">
                <div class="card-body p-4">
                    <span class="badge text-bg-warning text-dark mb-2">Administrateur</span>
                    <div class="rounded-circle bg-dark text-white d-inline-flex align-items-center justify-content-center mb-3" style="width:56px;height:56px">
                        <span class="fw-bold fs-4"><?= strtoupper(htmlspecialchars(substr($user->getPrenom(), 0, 1))) ?></span>
                    </div>
                    <h1 class="h5 fw-bold"><?= htmlspecialchars($user->getPrenom()) ?> <?= htmlspecialchars($user->getNom()) ?></h1>
                    <p class="small text-muted mb-3"><?= htmlspecialchars($user->getEmail()) ?></p>
                    <ul class="list-unstyled small mb-4">
                        <li class="mb-2"><span class="text-muted">Login</span><br><strong><?= htmlspecialchars($user->getLogin()) ?></strong></li>
                        <li><span class="text-muted">Rôle</span><br><span class="badge text-bg-dark"><?= htmlspecialchars($user->getRole()) ?></span></li>
                    </ul>
                    <p class="small text-muted mb-3">Cet espace regroupe les raccourcis de gestion. Les réservations clients ne s’affichent pas ici.</p>
                    <a href="?action=deconnexion" class="btn btn-outline-danger w-100">Se déconnecter</a>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h2 class="h5 fw-bold mb-0">Gestion du site</h2>
                    <p class="small text-muted mb-0">Accès rapide au back-office</p>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <a href="?action=home" class="btn btn-outline-dark w-100 py-3 text-start">
                                <i class="bi bi-house-door me-2"></i> Accueil administration
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="?action=admin_dashboard" class="btn btn-outline-primary w-100 py-3 text-start">
                                <i class="bi bi-speedometer2 me-2"></i> Tableau de bord
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="?action=vehicule_liste" class="btn btn-outline-success w-100 py-3 text-start">
                                <i class="bi bi-truck-front me-2"></i> Parc (gestion)
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="?action=agence_liste" class="btn btn-outline-info w-100 py-3 text-start">
                                <i class="bi bi-building me-2"></i> Agences
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="?action=admin_clients" class="btn btn-outline-warning w-100 py-3 text-start">
                                <i class="bi bi-people me-2"></i> Clients
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="?action=admin_staff" class="btn btn-outline-secondary w-100 py-3 text-start">
                                <i class="bi bi-shield-lock me-2"></i> Équipe admin
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="?action=catalogue_public" class="btn btn-outline-secondary w-100 py-3 text-start" target="_blank" rel="noopener">
                                <i class="bi bi-window me-2"></i> Catalogue public (aperçu)
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="?action=admin_notifications" class="btn btn-outline-dark w-100 py-3 text-start">
                                <i class="bi bi-bell me-2"></i> Notifications
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="?action=admin_messagerie" class="btn btn-outline-dark w-100 py-3 text-start">
                                <i class="bi bi-chat-dots me-2"></i> Messagerie
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="?action=admin_suivi_parc" class="btn btn-outline-primary w-100 py-3 text-start">
                                <i class="bi bi-broadcast-pin me-2"></i> Suivi parc (temps réel)
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="?action=admin_commentaires" class="btn btn-outline-danger w-100 py-3 text-start">
                                <i class="bi bi-star-half me-2"></i> Avis &amp; commentaires
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="?action=admin_parc_voitures" class="btn btn-light border w-100 py-3 text-start">
                                <i class="bi bi-car-front me-2"></i> Parc voitures
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="?action=admin_parc_motos" class="btn btn-light border w-100 py-3 text-start">
                                <i class="bi bi-speedometer2 me-2"></i> Parc motos
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="?action=admin_parc_camions" class="btn btn-light border w-100 py-3 text-start">
                                <i class="bi bi-truck me-2"></i> Parc camions
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
