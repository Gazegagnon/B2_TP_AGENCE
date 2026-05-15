<?php
/** @var Vehicule[] $vehicules @var string $token */
?>
<div class="admin-parc-header bg-dark text-white py-4 mb-0">
    <div class="container">
        <nav aria-label="Fil d'Ariane" class="mb-2">
            <ol class="breadcrumb mb-0 dark-breadcrumb">
                <li class="breadcrumb-item"><a href="?action=home" class="text-white-50">Accueil admin</a></li>
                <li class="breadcrumb-item text-white active" aria-current="page">Parc véhicules</li>
            </ol>
        </nav>
        <span class="badge text-bg-warning text-dark mb-2">Gestion — back-office</span>
        <h1 class="h3 fw-bold mb-1">Catalogue administrateur (parc)</h1>
        <p class="text-white-50 small mb-0">Vue tableau pour inventaire, ajout et suppression. Ce n’est pas la vitrine client.</p>
    </div>
</div>

<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <p class="small text-muted mb-0">Comparer avec le <a href="?action=catalogue_public" target="_blank" rel="noopener">catalogue public</a> (cartes clients).</p>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary" href="?action=catalogue_public" target="_blank" rel="noopener">Aperçu public</a>
            <a class="btn btn-primary" href="?action=vehicule_ajouter"><i class="bi bi-plus-lg me-1"></i> Ajouter</a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden border-start border-4 border-warning">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Aperçu</th>
                        <th>Véhicule</th>
                        <th>Prix / j</th>
                        <th>Agence</th>
                        <th>Type</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($vehicules as $veh): ?>
                    <tr>
                        <td style="width: 96px">
                            <img src="<?= htmlspecialchars($veh->getPhoto(), ENT_QUOTES, 'UTF-8') ?>" alt="" class="rounded" style="width:72px;height:48px;object-fit:cover;">
                        </td>
                        <td>
                            <strong><?= htmlspecialchars((string) $veh->getMarque()) ?></strong>
                            <?= htmlspecialchars((string) $veh->getModele()) ?>
                        </td>
                        <td><span class="fw-semibold text-primary"><?= htmlspecialchars((string) $veh->getPrixJournalier()) ?> €</span></td>
                        <td><?= htmlspecialchars((string) $veh->getAgence()->getNom()) ?></td>
                        <td><span class="badge text-bg-secondary text-capitalize"><?= htmlspecialchars((string) $veh->getType()) ?></span></td>
                        <td class="text-end text-nowrap">
                            <a class="btn btn-sm btn-primary" href="?action=vehicule_modifier&id=<?= (int) $veh->getId() ?>">Modifier</a>
                            <a class="btn btn-sm btn-outline-primary" href="?action=vehicule_detail&id=<?= (int) $veh->getId() ?>">Fiche</a>
                            <form action="?action=vehicule_supprimer" method="post" class="d-inline" data-confirm="Supprimer ce véhicule ? Cette action est irréversible.">
                                <input type="hidden" name="id" value="<?= (int) $veh->getId() ?>">
                                <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">
                                <button class="btn btn-sm btn-outline-danger" type="submit">Suppr.</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.dark-breadcrumb .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,.4); }
</style>
