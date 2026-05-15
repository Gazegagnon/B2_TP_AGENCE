<?php
/**
 * @var Vehicule $vehicule
 * @var string   $token
 * @var string|null $erreur_reservation
 */
$agence = $vehicule->getAgence();
$agenceNom = htmlspecialchars((string) $agence->getNom(), ENT_QUOTES, 'UTF-8');
$prixJ = (float) $vehicule->getPrixJournalier();
$isAdmin = (($_SESSION['role'] ?? '') === 'ADMIN');
$catalogueCrHref = $isAdmin ? '?action=vehicule_liste' : '?action=catalogue_public';
$catalogueCrLabel = $isAdmin ? 'Parc (gestion)' : 'Catalogue';
$accueilLabel = $isAdmin ? 'Accueil admin' : 'Accueil';
$detailHref = '?action=vehicule_detail&id=' . (int) $vehicule->getId();
$vid = (int) $vehicule->getId();
?>
<div class="lap-reservation-page">
    <section class="lap-page-header border-bottom">
        <div class="container py-3">
            <nav aria-label="Fil d'Ariane" class="mb-2">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="?action=home"><?= htmlspecialchars($accueilLabel, ENT_QUOTES, 'UTF-8') ?></a></li>
                    <li class="breadcrumb-item"><a href="<?= htmlspecialchars($catalogueCrHref, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($catalogueCrLabel, ENT_QUOTES, 'UTF-8') ?></a></li>
                    <li class="breadcrumb-item"><a href="<?= htmlspecialchars($detailHref, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $vehicule->getMarque()) ?> <?= htmlspecialchars((string) $vehicule->getModele()) ?></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Réservation</li>
                </ol>
            </nav>
            <span class="badge lap-badge-pill mb-2">Étape réservation</span>
            <h1 class="h2 mb-1">Finaliser votre location</h1>
            <p class="text-muted mb-0 small">Choisissez vos dates · Estimation immédiate · Confirmation dans votre espace</p>
        </div>
    </section>

    <div class="container py-4 py-lg-5">
        <div class="row g-4 g-xl-5">
            <div class="col-lg-5 order-lg-2">
                <div class="card border-0 shadow-sm rounded-4 lap-res-summary sticky-lg-top" style="top: 5.75rem;">
                    <div class="card-body p-4">
                        <h2 class="h6 text-uppercase text-muted letter-spacing-1 mb-3">Récapitulatif</h2>
                        <div class="d-flex gap-3 mb-3">
                            <img src="<?= htmlspecialchars($vehicule->getPhoto(), ENT_QUOTES, 'UTF-8') ?>" alt="" class="rounded-3 flex-shrink-0 object-fit-cover" width="96" height="72">
                            <div>
                                <p class="fw-bold mb-1"><?= htmlspecialchars((string) $vehicule->getMarque()) ?> <?= htmlspecialchars((string) $vehicule->getModele()) ?></p>
                                <p class="small text-muted mb-0 text-capitalize"><?= htmlspecialchars((string) $vehicule->getType()) ?> · <?= $agenceNom ?></p>
                            </div>
                        </div>
                        <p class="display-6 fw-bold text-primary mb-0 lap-price-tag"><?= htmlspecialchars((string) $vehicule->getPrixJournalier()) ?> €</p>
                        <p class="small text-muted">par jour (affichage indicatif)</p>
                        <hr class="my-3">
                        <ul class="small text-muted mb-0 ps-3 lap-checklist">
                            <li>Les dates définitives et le montant facturé sont validés en agence.</li>
                            <li>En cas d’indisponibilité, nous vous proposerons une alternative équivalente.</li>
                        </ul>
                        <a href="<?= htmlspecialchars($detailHref, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-link btn-sm px-0 mt-3">← Retour à la fiche détaillée</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-7 order-lg-1">
                <?php if (!empty($erreur_reservation ?? '')): ?>
                    <div class="alert alert-warning rounded-4 border-0 shadow-sm d-flex gap-2 align-items-start" role="alert">
                        <i class="bi bi-exclamation-triangle-fill flex-shrink-0 mt-1" aria-hidden="true"></i>
                        <div><?= htmlspecialchars($erreur_reservation, ENT_QUOTES, 'UTF-8') ?></div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($_SESSION['user_id'])): ?>
                    <div class="card border-0 shadow-sm rounded-4 lap-res-form-card">
                        <div class="card-body p-4 p-md-5">
                            <h2 class="h5 fw-bold mb-4">Vos dates de location</h2>
                            <form action="?action=vehicule_reserver&id=<?= $vid ?>" method="post" class="needs-validation" novalidate>
                                <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="id_vehicule" value="<?= $vid ?>">
                                <span id="res_prix_jour" class="d-none" data-prix="<?= htmlspecialchars((string) $prixJ, ENT_QUOTES, 'UTF-8') ?>"></span>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="res_debut" class="form-label fw-semibold">Date de début</label>
                                        <input type="date" name="debut" id="res_debut" class="form-control form-control-lg lap-input-date" required autocomplete="off">
                                        <div class="form-text">Premier jour de prise en charge du véhicule.</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="res_fin" class="form-label fw-semibold">Date de fin</label>
                                        <input type="date" name="fin" id="res_fin" class="form-control form-control-lg lap-input-date" required autocomplete="off">
                                        <div class="form-text">Dernier jour de location (restitution avant la fermeture indiquée par l’agence).</div>
                                    </div>
                                </div>

                                <div class="rounded-4 bg-light border p-4 my-4">
                                    <p class="small text-uppercase text-muted mb-1 fw-bold">Estimation provisoire</p>
                                    <p class="mb-0 fs-5 fw-bold text-dark" id="res_estime">—</p>
                                    <p class="small text-muted mt-2 mb-0">Calcul : nombre de jours de location (inclusif) × tarif journalier affiché. Frais annexes le cas échéant.</p>
                                </div>

                                <button type="submit" class="btn btn-success btn-lg rounded-pill fw-bold px-5 w-100 w-md-auto">Confirmer la réservation</button>
                            </form>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4 mt-4 border-start border-4 border-primary">
                        <div class="card-body p-4">
                            <h2 class="h6 fw-bold mb-3">Conseils &amp; suggestions</h2>
                            <ul class="small text-muted mb-0 ps-3 lap-checklist">
                                <li><strong>Durée :</strong> pour un week-end, sélectionnez du vendredi au dimanche (ou lundi si retour matinal).</li>
                                <li><strong>Flexibilité :</strong> si vous hésitez entre deux modèles, validez d’abord les dates — vous pourrez ajuster avec l’agence.</li>
                                <li><strong>Compte client :</strong> retrouvez l’historique et le statut dans « Mon compte » après envoi du formulaire.</li>
                                <li><strong>Besoin d’aide ?</strong> Contactez l’agence <strong><?= $agenceNom ?></strong> pour contraintes horaires ou équipements spécifiques.</li>
                            </ul>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card border-0 shadow-lg rounded-4 lap-guest-gate">
                        <div class="card-body p-4 p-md-5 text-center">
                            <i class="bi bi-person-lock display-4 text-primary d-block mb-3" aria-hidden="true"></i>
                            <h2 class="h5 fw-bold">Connexion requise</h2>
                            <p class="text-muted mb-4">Pour réserver, connectez-vous ou créez un compte en quelques instants. Vous serez redirigé vers cette page après identification.</p>
                            <div class="d-flex flex-wrap gap-2 justify-content-center">
                                <a href="?action=connexion" class="btn btn-primary btn-lg rounded-pill px-4">Se connecter</a>
                                <a href="?action=inscription" class="btn btn-outline-primary btn-lg rounded-pill px-4">Créer un compte</a>
                            </div>
                            <p class="small text-muted mt-4 mb-0">
                                <a href="<?= htmlspecialchars($detailHref, ENT_QUOTES, 'UTF-8') ?>">Continuer la lecture de la fiche sans réserver</a>
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
