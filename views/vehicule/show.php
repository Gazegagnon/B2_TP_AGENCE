<?php
/**
 * @var Vehicule $vehicule
 * @var array<int, Commentaire> $commentaires
 * @var Vehicule[] $suggestions
 * @var bool $canReview
 * @var bool $alreadyCommented
 * @var string $token
 * @var array{email: string, phone_display: string, phone_href: string} $support
 */
$agence = $vehicule->getAgence();
$agenceNom = htmlspecialchars((string) $agence->getNom(), ENT_QUOTES, 'UTF-8');
$isAdmin = (($_SESSION['role'] ?? '') === 'ADMIN');
$catalogueCrHref = $isAdmin ? '?action=vehicule_liste' : '?action=catalogue_public';
$catalogueCrLabel = $isAdmin ? 'Parc (gestion)' : 'Catalogue';
$accueilLabel = $isAdmin ? 'Accueil admin' : 'Accueil';
$typeLabel = htmlspecialchars((string) $vehicule->getType(), ENT_QUOTES, 'UTF-8');
$reserverHref = '?action=vehicule_reserver&id=' . (int) $vehicule->getId();
?>
<div class="lap-vehicle-page">
    <section class="lap-page-header border-bottom">
        <div class="container py-3">
            <nav aria-label="Fil d'Ariane" class="mb-2">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="?action=home"><?= htmlspecialchars($accueilLabel, ENT_QUOTES, 'UTF-8') ?></a></li>
                    <li class="breadcrumb-item"><a href="<?= htmlspecialchars($catalogueCrHref, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($catalogueCrLabel, ENT_QUOTES, 'UTF-8') ?></a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars((string) $vehicule->getMarque()) ?> <?= htmlspecialchars((string) $vehicule->getModele()) ?></li>
                </ol>
            </nav>
            <span class="badge lap-badge-pill mb-2">Fiche véhicule</span>
            <h1 class="h2 mb-1"><?= htmlspecialchars((string) $vehicule->getMarque()) ?> <?= htmlspecialchars((string) $vehicule->getModele()) ?></h1>
            <p class="text-muted mb-0 small">Retrait en agence partenaire · Tarifs transparents</p>
        </div>
    </section>

    <div class="container py-4 py-lg-5">
        <div class="row g-4 g-xl-5">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 lap-vehicle-hero-card">
                    <div class="ratio ratio-16x9 bg-light">
                        <img src="<?= htmlspecialchars($vehicule->getPhoto(), ENT_QUOTES, 'UTF-8') ?>"
                             class="object-fit-cover"
                             alt="<?= htmlspecialchars((string) $vehicule->getMarque() . ' ' . (string) $vehicule->getModele(), ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span class="badge rounded-pill lap-chip-type text-capitalize"><?= $typeLabel ?></span>
                            <span class="badge rounded-pill lap-chip-state">État : <?= htmlspecialchars((string) $vehicule->getEtat(), ENT_QUOTES, 'UTF-8') ?></span>
                        </div>

                        <h2 class="h5 fw-bold text-uppercase small text-muted letter-spacing-1 mb-3">À propos de ce modèle</h2>
                        <?php
                        $tRaw = (string) $vehicule->getType();
                        $aboutText = match ($tRaw) {
                            'voiture' => 'Véhicule polyvalent pour la ville et les longs trajets. Idéal pour un usage professionnel ou des escapades en famille.',
                            'moto' => 'Deux-roues léger pour se déplacer avec agilité. Pensez à votre équipement de protection et au stationnement sécurisé.',
                            'camion' => 'Utilitaire pour charges volumineuses ou activités pro. Vérifiez la hauteur autorisée et les accès de livraison.',
                            default => 'Véhicule disponible à la location avec les garanties LocAuto Pro et le suivi en agence.',
                        };
                        ?>
                        <p class="text-muted mb-0 lap-lead-copy"><?= htmlspecialchars($aboutText, ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="h5 fw-bold mb-4 d-flex align-items-center gap-2">
                            <i class="bi bi-list-columns-reverse text-primary" aria-hidden="true"></i> Caractéristiques
                        </h2>
                        <div class="table-responsive">
                            <table class="table table-borderless lap-spec-table mb-0">
                                <tbody>
                                <tr><th scope="row">Type</th><td class="text-capitalize"><?= $typeLabel ?></td></tr>
                                <tr><th scope="row">Couleur</th><td><?= htmlspecialchars((string) $vehicule->getCouleur(), ENT_QUOTES, 'UTF-8') ?></td></tr>
                                <tr><th scope="row">Poids</th><td><?= htmlspecialchars((string) $vehicule->getPoids(), ENT_QUOTES, 'UTF-8') ?> kg</td></tr>
                                <tr><th scope="row">Capacité</th><td><?= htmlspecialchars((string) $vehicule->getCapacite(), ENT_QUOTES, 'UTF-8') ?> places</td></tr>
                                <?php if ($vehicule instanceof Voiture || $vehicule instanceof Camion) : ?>
                                    <tr><th scope="row">Nombre de portes</th><td><?= htmlspecialchars((string) $vehicule->getNombre_porte(), ENT_QUOTES, 'UTF-8') ?></td></tr>
                                <?php endif; ?>
                                <?php if ($vehicule instanceof Camion) : ?>
                                    <tr><th scope="row">Longueur</th><td><?= htmlspecialchars((string) $vehicule->getLongueur(), ENT_QUOTES, 'UTF-8') ?> cm</td></tr>
                                <?php endif; ?>
                                <?php if ($vehicule instanceof DeuxRoues) : ?>
                                    <tr><th scope="row">Cylindrée</th><td><?= htmlspecialchars((string) $vehicule->getCylindre(), ENT_QUOTES, 'UTF-8') ?> cm³</td></tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 mb-4 lap-agency-card">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="h5 fw-bold mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-building text-primary" aria-hidden="true"></i> Lieu de retrait
                        </h2>
                        <p class="fw-semibold mb-1"><?= $agenceNom ?></p>
                        <p class="text-muted small mb-0">
                            <?= htmlspecialchars((string) $agence->getAdresse(), ENT_QUOTES, 'UTF-8') ?><br>
                            <?= htmlspecialchars((string) $agence->getCp(), ENT_QUOTES, 'UTF-8') ?>
                            <?= htmlspecialchars((string) $agence->getVille(), ENT_QUOTES, 'UTF-8') ?>
                        </p>
                        <p class="small text-muted mt-3 mb-0">Les horaires d’ouverture et le plan d’accès vous seront confirmés par e-mail après réservation.</p>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 mb-4 border-start border-4 border-warning">
                    <div class="card-body p-4">
                        <h2 class="h6 fw-bold mb-3"><i class="bi bi-lightbulb me-2" aria-hidden="true"></i>Bon à savoir avant de réserver</h2>
                        <ul class="small text-muted mb-0 ps-3 lap-checklist">
                            <li><strong>Documents :</strong> permis de conduire valide et pièce d’identité au même nom que la réservation.</li>
                            <li><strong>Âge &amp; ancienneté :</strong> nous vous recommandons d’avoir au moins 2 ans de permis pour les catégories citadines et berlines.</li>
                            <li><strong>Carburant :</strong> véhicule fourni avec le niveau indiqué en agence ; restituez-le au même niveau pour éviter des frais.</li>
                            <li><strong>Assurance :</strong> garanties de base incluses ; franchise et options peuvent être précisées lors du retrait.</li>
                        </ul>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="h5 fw-bold mb-3">Avis clients</h2>
                        <?php if (!empty($commentaires)) : ?>
                            <ul class="list-unstyled mb-0">
                                <?php foreach ($commentaires as $comment) : ?>
                                    <li class="border-bottom pb-3 mb-3">
                                        <div class="d-flex justify-content-between flex-wrap gap-1">
                                            <strong><?= htmlspecialchars((string) $comment->getPersonne()->getPrenom()) ?></strong>
                                            <span class="small text-muted"><?= htmlspecialchars((string) $comment->getDateComment()) ?></span>
                                        </div>
                                        <?php $n = $comment->getNote(); ?>
                                        <?php if ($n !== null && $n > 0): ?>
                                            <p class="small text-warning mb-1 mt-1" aria-label="Note <?= (int) $n ?> sur 5"><?= str_repeat('★', min(5, (int) $n)) ?><?= str_repeat('☆', max(0, 5 - (int) $n)) ?></p>
                                        <?php endif; ?>
                                        <p class="mb-0 mt-2 text-muted"><?= htmlspecialchars((string) $comment->getComment()) ?></p>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-muted mb-0">Pas encore d’avis sur ce véhicule. Après votre location, nous vous invitons à partager votre retour.</p>
                        <?php endif; ?>

                        <?php if (!empty($_SESSION['user_id']) && !$isAdmin): ?>
                            <?php if (!empty($canReview)): ?>
                                <div class="border-top pt-4 mt-4">
                                    <h3 class="h6 fw-bold mb-3">Donner votre avis</h3>
                                    <p class="small text-muted mb-3">Vous avez réservé ce véhicule : partagez une note et un court commentaire.</p>
                                    <form method="post" action="?action=vehicule_commenter" class="vstack gap-3">
                                        <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">
                                        <input type="hidden" name="id_vehicule" value="<?= (int) $vehicule->getId() ?>">
                                        <div>
                                            <label class="form-label small">Note (1 à 5)</label>
                                            <select name="note" class="form-select" required>
                                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                                    <option value="<?= $i ?>"><?= $i ?> — <?= str_repeat('★', $i) ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="form-label small">Commentaire</label>
                                            <textarea name="commentaire" class="form-control" rows="4" required minlength="3" placeholder="Accueil, confort, propreté…"></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Publier mon avis</button>
                                    </form>
                                </div>
                            <?php elseif (!empty($alreadyCommented)): ?>
                                <p class="small text-success mb-0 mt-3"><i class="bi bi-check-circle me-1"></i> Vous avez déjà laissé un avis pour ce véhicule. Merci !</p>
                            <?php else: ?>
                                <p class="small text-muted mb-0 mt-3">Réservation requise pour publier un avis sur ce modèle.</p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-lg rounded-4 sticky-lg-top lap-sticky-booking" style="top: 5.75rem;">
                    <div class="card-body p-4 p-md-4">
                        <p class="text-uppercase small text-muted mb-1 letter-spacing-1">À partir de</p>
                        <p class="display-6 fw-bold text-primary mb-0 lap-price-tag"><?= htmlspecialchars((string) $vehicule->getPrixJournalier()) ?> €</p>
                        <p class="small text-muted mb-4">par jour · TVA incluse — hors options, carburant et franchise éventuelle</p>

                        <div class="d-grid gap-2 mb-4">
                            <a href="<?= htmlspecialchars($reserverHref, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-success btn-lg rounded-pill fw-bold">Réserver en ligne</a>
                            <a href="<?= htmlspecialchars($catalogueCrHref, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-outline-secondary rounded-pill">Voir d’autres véhicules</a>
                        </div>

                        <div class="lap-mini-facts small text-muted border-top pt-3">
                            <p class="mb-2"><i class="bi bi-shield-check me-2 text-success" aria-hidden="true"></i> Véhicule contrôlé avant départ</p>
                            <p class="mb-2"><i class="bi bi-credit-card me-2" aria-hidden="true"></i> Paiement sécurisé au compte client</p>
                            <p class="mb-0"><i class="bi bi-arrow-repeat me-2" aria-hidden="true"></i> Modifiable selon disponibilité agence</p>
                        </div>

                        <?php if (!$isAdmin): ?>
                            <div class="border-top pt-3 mt-3 small">
                                <p class="fw-semibold mb-2">Service client</p>
                                <p class="mb-1"><i class="bi bi-chat-dots me-2"></i><a href="?action=client_messagerie">Messagerie</a></p>
                                <p class="mb-1"><i class="bi bi-envelope me-2"></i><a href="mailto:<?= htmlspecialchars($support['email'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($support['email'], ENT_QUOTES, 'UTF-8') ?></a></p>
                                <p class="mb-0"><i class="bi bi-telephone me-2"></i><a href="tel:<?= htmlspecialchars($support['phone_href'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($support['phone_display'], ENT_QUOTES, 'UTF-8') ?></a></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($suggestions)) : ?>
            <section class="mt-5 pt-4 border-top">
                <h2 class="h4 fw-bold mb-2 lap-display-sub">Vous pourriez aussi aimer</h2>
                <p class="text-muted small mb-4">Sélection proche de votre recherche (même catégorie ou tarif comparable).</p>
                <div class="row g-4">
                    <?php foreach ($suggestions as $sv) : ?>
                        <div class="col-md-6 col-xl-3">
                            <article class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden lap-suggest-card">
                                <a href="?action=vehicule_detail&id=<?= (int) $sv->getId() ?>" class="text-decoration-none text-dark">
                                    <img src="<?= htmlspecialchars($sv->getPhoto(), ENT_QUOTES, 'UTF-8') ?>" class="w-100 object-fit-cover" style="height: 140px;" alt="">
                                    <div class="card-body p-3">
                                        <h3 class="h6 fw-bold mb-1"><?= htmlspecialchars((string) $sv->getMarque()) ?> <?= htmlspecialchars((string) $sv->getModele()) ?></h3>
                                        <p class="small text-muted mb-2 text-capitalize"><?= htmlspecialchars((string) $sv->getType()) ?></p>
                                        <p class="fw-bold text-primary mb-0"><?= htmlspecialchars((string) $sv->getPrixJournalier()) ?> € <span class="small fw-normal text-muted">/ jour</span></p>
                                    </div>
                                </a>
                                <div class="card-footer bg-transparent border-0 pt-0 px-3 pb-3">
                                    <div class="d-grid gap-1">
                                        <a class="btn btn-sm btn-outline-primary rounded-pill" href="?action=vehicule_detail&id=<?= (int) $sv->getId() ?>">Fiche</a>
                                        <a class="btn btn-sm btn-primary rounded-pill" href="?action=vehicule_reserver&id=<?= (int) $sv->getId() ?>">Réserver</a>
                                    </div>
                                </div>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    </div>
</div>
