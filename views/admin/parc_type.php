<?php
/**
 * @var string $token
 * @var Vehicule[] $vehicules
 * @var string $type_filter
 * @var string $type_title
 */
?>
<div class="admin-shell">
<div class="container-fluid">
    <div class="row g-0">
        <?php include __DIR__ . '/inc_sidebar.php'; ?>
        <main class="col-lg-10 px-3 px-md-4 py-4">
            <nav aria-label="Fil d'Ariane" class="mb-3">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="?action=admin_dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active"><?= htmlspecialchars($type_title, ENT_QUOTES, 'UTF-8') ?></li>
                </ol>
            </nav>
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                <h1 class="h3 fw-bold mb-0">Parc — <?= htmlspecialchars($type_title, ENT_QUOTES, 'UTF-8') ?></h1>
                <div class="d-flex gap-2">
                    <a href="?action=vehicule_ajouter" class="btn btn-primary btn-sm">+ Ajouter</a>
                    <a href="?action=admin_suivi_parc" class="btn btn-outline-secondary btn-sm">Suivi</a>
                </div>
            </div>
            <div class="table-responsive card border-0 shadow-sm">
                <table class="table table-hover align-middle mb-0 admin-table">
                    <thead class="table-light">
                        <tr>
                            <th></th>
                            <th>Véhicule</th>
                            <th>Prix / j</th>
                            <th>Agence</th>
                            <th>État</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($vehicules as $veh): ?>
                        <tr>
                            <td style="width:88px">
                                <img src="<?= htmlspecialchars($veh->getPhoto(), ENT_QUOTES, 'UTF-8') ?>" alt="" class="rounded" width="72" height="48" style="object-fit:cover;">
                            </td>
                            <td>
                                <strong><?= htmlspecialchars((string) $veh->getMarque()) ?></strong>
                                <?= htmlspecialchars((string) $veh->getModele()) ?>
                            </td>
                            <td><?= htmlspecialchars((string) $veh->getPrixJournalier()) ?> €</td>
                            <td class="small"><?= htmlspecialchars($veh->getAgence()->getNom()) ?></td>
                            <td class="small text-capitalize"><?= htmlspecialchars((string) $veh->getEtat()) ?></td>
                            <td class="text-end text-nowrap">
                                <a class="btn btn-sm btn-primary" href="?action=vehicule_modifier&id=<?= (int) $veh->getId() ?>">Modifier</a>
                                <a class="btn btn-sm btn-outline-primary" href="?action=vehicule_detail&id=<?= (int) $veh->getId() ?>">Public</a>
                                <form action="?action=vehicule_supprimer" method="post" class="d-inline" data-confirm="Supprimer ce véhicule ?">
                                    <input type="hidden" name="id" value="<?= (int) $veh->getId() ?>">
                                    <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Suppr.</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($vehicules)): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">Aucun véhicule dans cette catégorie.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>
</div>
