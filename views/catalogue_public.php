<?php
/**
 * @var Vehicule[] $vehicules_voiture
 * @var Vehicule[] $vehicules_moto
 * @var Vehicule[] $vehicules_camion
 * @var int $total_results
 * @var string $filter_q
 * @var string $filter_type
 * @var float|null $filter_prix_max
 * @var string $filter_tri
 * @var int $total_parc
 * @var int $total_agences
 * @var array{email: string, phone_display: string, phone_href: string} $support
 */
$showVoit = $filter_type === '' || $filter_type === 'voiture';
$showMoto = $filter_type === '' || $filter_type === 'moto';
$showCam = $filter_type === '' || $filter_type === 'camion';

$sections = [
    [
        'slug' => 'voitures',
        'title' => 'Voitures',
        'lead' => 'Citadines, berlines, SUV…',
        'icon' => 'bi-car-front',
        'list' => $vehicules_voiture,
        'show' => $showVoit,
    ],
    [
        'slug' => 'camions',
        'title' => 'Camions & utilitaires',
        'lead' => 'Charges, livraisons, emménagements.',
        'icon' => 'bi-truck',
        'list' => $vehicules_camion,
        'show' => $showCam,
    ],
    [
        'slug' => 'motos',
        'title' => 'Motos & deux-roues',
        'lead' => 'Mobilité légère et urbaine.',
        'icon' => 'bi-speedometer2',
        'list' => $vehicules_moto,
        'show' => $showMoto,
    ],
];
?>
<section class="lap-page-header border-bottom">
    <div class="container py-4">
        <span class="badge lap-badge-pill mb-2">Location — espace public</span>
        <h1 class="h3 mb-1">Catalogue des véhicules</h1>
        <p class="text-muted small mb-0">
            Parc segmenté par catégorie. <?= (int) $total_parc ?> véhicules référencés · <?= (int) $total_agences ?> agence<?= $total_agences > 1 ? 's' : '' ?>.
            Besoin d’aide ? <a href="?action=client_messagerie">Messagerie</a>, <a href="mailto:<?= htmlspecialchars($support['email'], ENT_QUOTES, 'UTF-8') ?>">e-mail</a> ou <a href="tel:<?= htmlspecialchars($support['phone_href'], ENT_QUOTES, 'UTF-8') ?>">téléphone</a>.
        </p>
    </div>
</section>

<section class="py-4 bg-light border-bottom lap-catalog-toolbar">
    <div class="container">
        <form method="get" class="row g-3 align-items-end">
            <input type="hidden" name="action" value="catalogue_public">
            <div class="col-12 col-lg-4">
                <label class="form-label small text-muted text-uppercase mb-1">Recherche</label>
                <input type="search" name="q" class="form-control" placeholder="Marque, modèle…" value="<?= htmlspecialchars($filter_q, ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <label class="form-label small text-muted text-uppercase mb-1">Type</label>
                <select name="type" class="form-select">
                    <option value="">Toutes catégories</option>
                    <option value="voiture" <?= $filter_type === 'voiture' ? 'selected' : '' ?>>Voitures</option>
                    <option value="camion" <?= $filter_type === 'camion' ? 'selected' : '' ?>>Camions</option>
                    <option value="moto" <?= $filter_type === 'moto' ? 'selected' : '' ?>>Motos</option>
                </select>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <label class="form-label small text-muted text-uppercase mb-1">Budget max / j</label>
                <input type="number" name="prix_max" class="form-control" step="1" min="0" placeholder="€" value="<?= $filter_prix_max !== null ? htmlspecialchars((string) $filter_prix_max, ENT_QUOTES, 'UTF-8') : '' ?>">
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <label class="form-label small text-muted text-uppercase mb-1">Tri</label>
                <select name="tri" class="form-select">
                    <option value="marque" <?= $filter_tri === 'marque' ? 'selected' : '' ?>>Modèle A → Z</option>
                    <option value="prix_asc" <?= $filter_tri === 'prix_asc' ? 'selected' : '' ?>>Prix ↑</option>
                    <option value="prix_desc" <?= $filter_tri === 'prix_desc' ? 'selected' : '' ?>>Prix ↓</option>
                </select>
            </div>
            <div class="col-6 col-lg-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">Filtrer</button>
                <a href="?action=catalogue_public" class="btn btn-outline-secondary" title="Réinitialiser">↺</a>
            </div>
        </form>
        <p class="small text-muted mb-0 mt-3">
            <strong><?= (int) $total_results ?></strong> véhicule<?= $total_results > 1 ? 's' : '' ?> correspondent à vos critères.
            <?php if ($filter_type !== ''): ?>
                <span class="ms-1">Filtre actif sur une catégorie — les autres blocs sont masqués.</span>
            <?php endif; ?>
        </p>
    </div>
</section>

<section class="py-5" id="catalogue">
    <div class="container">
        <?php foreach ($sections as $sec): ?>
            <?php if (!$sec['show']) {
                continue;
            } ?>
            <div class="mb-5 pb-3 lap-catalog-section" id="<?= htmlspecialchars($sec['slug'], ENT_QUOTES, 'UTF-8') ?>">
                <div class="d-flex flex-wrap align-items-end justify-content-between gap-2 mb-4">
                    <div>
                        <h2 class="h4 fw-bold mb-1 d-flex align-items-center gap-2">
                            <i class="bi <?= htmlspecialchars($sec['icon'], ENT_QUOTES, 'UTF-8') ?> text-primary" aria-hidden="true"></i>
                            <?= htmlspecialchars($sec['title'], ENT_QUOTES, 'UTF-8') ?>
                        </h2>
                        <p class="text-muted small mb-0"><?= htmlspecialchars($sec['lead'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <span class="badge text-bg-secondary rounded-pill"><?= count($sec['list']) ?> véhicule<?= count($sec['list']) > 1 ? 's' : '' ?></span>
                </div>

                <?php if (!empty($sec['list'])) : ?>
                    <div class="row g-4">
                        <?php foreach ($sec['list'] as $v) : ?>
                            <div class="col-md-6 col-xl-4">
                                <article class="card card-vehicle h-100 position-relative">
                                    <img
                                        src="<?= !empty($v->getImg()) ? htmlspecialchars($v->getPhoto(), ENT_QUOTES, 'UTF-8') : 'https://via.placeholder.com/400x240?text=Véhicule' ?>"
                                        class="card-img-top"
                                        alt="<?= htmlspecialchars((string) $v->getMarque() . ' ' . (string) $v->getModele(), ENT_QUOTES, 'UTF-8') ?>"
                                        loading="lazy"
                                    >
                                    <div class="card-body d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                            <h3 class="h5 card-title mb-0"><?= htmlspecialchars((string) $v->getMarque()) ?> <?= htmlspecialchars((string) $v->getModele()) ?></h3>
                                            <span class="badge text-bg-secondary text-capitalize"><?= htmlspecialchars((string) $v->getType()) ?></span>
                                        </div>
                                        <p class="text-muted small mb-2"><?= htmlspecialchars((string) $v->getAgence()->getNom()) ?></p>
                                        <p class="h5 text-primary mb-3"><?= htmlspecialchars((string) $v->getPrix_journalier()) ?> € <span class="fs-6 text-muted fw-normal">/ jour</span></p>
                                        <div class="d-grid gap-2 mt-auto">
                                            <a href="?action=vehicule_detail&id=<?= (int) $v->getId() ?>" class="btn btn-primary rounded-pill fw-semibold">Fiche détaillée</a>
                                            <a href="?action=vehicule_reserver&id=<?= (int) $v->getId() ?>" class="btn btn-outline-success rounded-pill fw-semibold">Réserver</a>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <div class="alert alert-light border mb-0">
                        Aucun véhicule dans cette section pour l’instant ou avec ces filtres.
                        <a href="?action=catalogue_public">Réinitialiser la recherche</a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <?php if ($total_results === 0) : ?>
            <div class="alert alert-info" role="alert">
                Aucun résultat global. Élargissez le budget ou retirez le mot-clé.
            </div>
        <?php endif; ?>
    </div>
</section>
